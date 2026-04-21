<x-layouts.app :title="__('Farm Produces Ilocos Norte')">

    <div class="p-6 space-y-4">
        <h1 class="text-2xl font-semibold">
            All Available Farm Produces
        </h1>

        <!-- FILTERS -->
        <div class="flex gap-4 items-center">
            <!-- PRODUCT -->
            <select id="produceFilter" class="border rounded-lg p-2 text-sm w-64">
                <option value="">All Produce</option>
                @foreach ($products as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                @endforeach
            </select>

            <!-- MUNICIPALITY -->
            <select id="municipalityFilter" class="border rounded-lg p-2 text-sm w-64">
                <option value="">All Municipalities</option>
            </select>
        </div>

        <!-- MAP -->
        <div wire:ignore id="map" class="w-full h-[80vh] rounded-xl border border-zinc-300 dark:border-zinc-700">
        </div>
    </div>

    <script>
        const produces = @json($produces);

        let map;
        let markerLayer;

        function initIlocosProductsMap() {
            const el = document.getElementById('map');
            if (!el || el.dataset.loaded) return;
            el.dataset.loaded = true;

            map = L.map(el).setView([18.1647, 120.7116], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            markerLayer = L.layerGroup().addTo(map);

            fetch('/maps/ilocos-norte.geojson')
                .then(res => res.json())
                .then(data => {

                    L.geoJSON(data, {
                        style: {
                            color: '#2563eb',
                            weight: 2,
                            fillOpacity: 0.2
                        }
                    }).addTo(map);

                    populateMunicipalities(data);

                    applyFilters();

                    setTimeout(() => map.invalidateSize(), 300);
                });
        }

        function populateMunicipalities(data) {
            const select = document.getElementById('municipalityFilter');
            const municipalities = new Set();

            data.features.forEach(f => {
                municipalities.add(f.properties.Municipality);
            });

            [...municipalities].sort().forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                select.appendChild(opt);
            });
        }

        function applyFilters() {
            const selectedProduct = document.getElementById('produceFilter').value;
            const selectedMunicipality = document.getElementById('municipalityFilter').value;

            let filtered = produces;

            if (selectedProduct) {
                filtered = filtered.filter(p =>
                    p.product.toLowerCase() === selectedProduct.toLowerCase()
                );
            }

            if (selectedMunicipality) {
                filtered = filtered.filter(p =>
                    p.farmer &&
                    p.farmer.municipality === selectedMunicipality
                );
            }

            renderMarkers(filtered);
        }

        function isValidProduce(p) {
            const now = new Date();

            if (!p.available_from || !p.available_until) return false;

            const from = new Date(String(p.available_from).replace(' ', 'T'));
            const until = new Date(String(p.available_until).replace(' ', 'T'));

            if (isNaN(from) || isNaN(until)) return false;

            return (
                p.status === 'available' &&
                now >= from &&
                now <= until
            );
        }

        function formatDate(dateStr) {
            if (!dateStr) return '—';

            const d = new Date(String(dateStr).replace(' ', 'T'));
            if (isNaN(d)) return '—';

            return d.toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'short',
                day: '2-digit'
            });
        }

        function renderMarkers(filteredProduces) {

            markerLayer.clearLayers();

            const valid = filteredProduces.filter(isValidProduce);

            const grouped = {};

            valid.forEach(p => {
                if (!p.farmer?.latitude || !p.farmer?.longitude) return;

                if (!grouped[p.farmer.id]) {
                    grouped[p.farmer.id] = {
                        farmer: p.farmer,
                        produces: []
                    };
                }

                grouped[p.farmer.id].produces.push(p);
            });

            Object.values(grouped).forEach(entry => {

                const farmer = entry.farmer;
                const products = entry.produces;

                // 🖼 STACKED IMAGES
                let stackedImages = products.slice(0, 4).map(p => {
                    return p.image ?
                        `<img src="/storage/${p.image}" class="w-8 h-8 rounded-full border-2 border-white object-cover shadow">` :
                        '';
                }).join('');

                const farmerImage = farmer.image ?
                    `/storage/${farmer.image}` :
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(farmer.name)}`;

                let rows = '';

                products.forEach(prod => {
                    const preorderUrl = `/customer/preorders/create/${prod.id}`;

                    rows += `
                        <tr class="border-t">
                            <td class="px-2 py-1">${prod.product}</td>
                            <td class="px-2 py-1 text-center">${prod.quantity} Kgs</td>
                            <td class="px-2 py-1 text-right">₱${prod.price}</td>
                            <td class="px-2 py-1 text-center">
                                <a href="${preorderUrl}"
                                class="inline-flex items-center justify-center bg-green-600 !text-white text-xs px-3 py-1.5 rounded-md font-semibold hover:bg-green-700 transition">
                                Preorder
                                </a>
                            </td>
                        </tr>

                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-2 py-1 text-xs text-gray-600">
                                📅 ${formatDate(prod.available_from)} → ${formatDate(prod.available_until)}
                            </td>
                        </tr>
                    `;
                });

                const popup = `
                    <div class="w-[320px] text-sm">

                        <div class="flex items-center gap-2 mb-2">
                            <img src="${farmerImage}"
                                class="w-12 h-12 rounded-full border object-cover">

                            <div>
                                <strong>${farmer.name}</strong><br>
                                <span class="text-xs text-gray-500">
                                    ${farmer.barangay}, ${farmer.municipality}
                                </span>
                            </div>
                        </div>

                        <!-- STACKED IMAGES -->
                        <div class="flex -space-x-2 mb-2">
                            ${stackedImages}
                        </div>

                        <div class="max-h-40 overflow-y-auto border rounded">
                            <table class="w-full text-xs">
                                <tbody>${rows}</tbody>
                            </table>
                        </div>

                    </div>
                `;

                const icon = L.divIcon({
                    className: '',
                    html: `
                        <div class="relative flex items-center justify-center">

                            <!-- OUTER WHITE BORDER (visibility layer) -->
                            <div class="w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center">

                                <!-- INNER BORDER (blue accent) -->
                                <div class="w-10 h-10 rounded-full border-2 border-blue-600 overflow-hidden">

                                    <img
                                        src="${products[0].image ? '/storage/' + products[0].image : farmerImage}"
                                        class="w-full h-full object-cover"
                                    >

                                </div>
                            </div>

                            <!-- POINTER TIP -->
                            <div class="absolute -bottom-1 w-3 h-3 bg-white rotate-45 border-r border-b border-blue-600"></div>

                        </div>
                    `,
                    iconSize: [48, 52],
                    iconAnchor: [24, 52]
                });

                L.marker([farmer.latitude, farmer.longitude], {
                        icon
                    })
                    .addTo(markerLayer)
                    .bindPopup(popup, {
                        maxWidth: 340
                    });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initIlocosProductsMap();

            document.getElementById('produceFilter').addEventListener('change', applyFilters);
            document.getElementById('municipalityFilter').addEventListener('change', applyFilters);
        });

        document.addEventListener('livewire:navigated', initIlocosProductsMap);
    </script>

</x-layouts.app>
