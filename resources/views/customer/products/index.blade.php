<x-layouts.app :title="__('Farm Produces Ilocos Norte')">

    <div class="p-6 space-y-4">
        <h1 class="text-2xl font-semibold">
            All Available Farm Produces
        </h1>

        <!-- FILTERS -->
        <div class="flex gap-4 items-center">
            <select id="produceFilter" class="border rounded-lg p-2 text-sm w-64">
                <option value="">All Produce</option>
                @foreach ($products as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                @endforeach
            </select>

            <select id="municipalityFilter" class="border rounded-lg p-2 text-sm w-64">
                <option value="">All Municipalities</option>
            </select>

            <input type="text" id="farmerFilter" placeholder="Search farmer..."
                class="border rounded-lg p-2 text-sm w-64" />
        </div>

        <!-- MAP -->
        <div wire:ignore id="map" class="w-full h-[80vh] rounded-xl border border-zinc-300 dark:border-zinc-700">
        </div>
    </div>

    <script>
        const produces = @json($produces);

        let map;
        let markerLayer;
        let routingControl = null;
        let userLatLng = null;

        function initIlocosProductsMap() {
            const el = document.getElementById('map');
            if (!el || el.dataset.loaded) return;
            el.dataset.loaded = true;

            map = L.map(el).setView([18.1647, 120.7116], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            markerLayer = L.layerGroup().addTo(map);

            map.locate({
                setView: false
            });

            map.on('locationfound', function(e) {
                userLatLng = e.latlng;

                L.circleMarker(userLatLng, {
                    radius: 6,
                    color: 'blue'
                }).addTo(map).bindPopup("You are here");
            });

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
            const selectedProduct = document.getElementById('produceFilter').value.toLowerCase();
            const selectedMunicipality = document.getElementById('municipalityFilter').value;
            const farmerSearch = document.getElementById('farmerFilter').value.toLowerCase();

            let filtered = produces;

            if (selectedProduct) {
                filtered = filtered.filter(p =>
                    p.product.toLowerCase() === selectedProduct
                );
            }

            if (selectedMunicipality) {
                filtered = filtered.filter(p =>
                    p.farmer &&
                    p.farmer.municipality === selectedMunicipality
                );
            }

            if (farmerSearch) {
                filtered = filtered.filter(p =>
                    p.farmer &&
                    p.farmer.name.toLowerCase().includes(farmerSearch)
                );
            }

            renderMarkers(filtered);
        }

        function isValidProduce(p) {
            const now = new Date();

            if (!p.available_from || !p.available_until) return false;

            const from = new Date(String(p.available_from).replace(' ', 'T'));
            const until = new Date(String(p.available_until).replace(' ', 'T'));

            return (
                p.status === 'available' &&
                now >= from &&
                now <= until
            );
        }

        function formatDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(String(dateStr).replace(' ', 'T'));
            return isNaN(d) ? '—' : d.toLocaleDateString('en-PH');
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

                // ⭐ calculate rating safely
                const avgRating = farmer.average_rating ?
                    parseFloat(farmer.average_rating).toFixed(1) :
                    '0.0';

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
                                    class="bg-green-600 !text-white text-xs px-3 py-1.5 rounded font-semibold hover:bg-green-700">
                                    Preorder
                                </a>
                            </td>
                        </tr>
                    `;
                });

                const farmerImage = farmer.image ?
                    `/storage/${farmer.image}` :
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(farmer.name)}`;

                // ⭐ RATING COMMENTS HTML
                let reviewsHtml = '';

                if (farmer.ratings && farmer.ratings.length > 0) {
                    reviewsHtml = farmer.ratings.slice(0, 2).map(r => `
                        <div class="text-xs border-b py-1">
                            <span class="text-yellow-500">★ ${r.rating}</span>
                            <span class="text-gray-600">
                                ${r.comment ? `"${r.comment}"` : 'No comment'}
                            </span>
                            <div class="text-[10px] text-gray-400">
                                by ${r.customer?.name ?? 'User'}
                            </div>
                        </div>
                    `).join('');
                } else {
                    reviewsHtml = `<div class="text-xs text-gray-400">No reviews yet</div>`;
                }

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

                                <div class="text-xs text-yellow-600 mt-1">
                                    ⭐ ${avgRating} / 5
                                </div>
                            </div>
                        </div>

                        <div class="max-h-40 overflow-y-auto border rounded">
                            <table class="w-full text-xs">
                                <tbody>${rows}</tbody>
                            </table>
                        </div>

                        <!-- 💬 REVIEWS -->
                        <div class="mt-2 border-t pt-2">
                            <div class="text-xs font-semibold mb-1">Recent Reviews</div>
                            ${reviewsHtml}
                        </div>

                        <button onclick="routeTo(${farmer.latitude}, ${farmer.longitude})"
                            class="mt-2 w-full bg-blue-600 text-white text-xs py-2 rounded hover:bg-blue-700">
                            📍 Route Here
                        </button>

                    </div>
                `;

                const icon = L.divIcon({
                    className: '',
                    html: `
                        <div class="relative flex items-center justify-center">
                            <div class="w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center">
                                <div class="w-10 h-10 rounded-full border-2 border-blue-600 overflow-hidden">
                                    <img src="${products[0].image ? '/storage/' + products[0].image : farmerImage}"
                                         class="w-full h-full object-cover">
                                </div>
                            </div>
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
                    .bindPopup(popup);
            });
        }

        function routeTo(lat, lng) {
            if (!userLatLng) {
                alert("Location not detected yet.");
                return;
            }

            if (routingControl) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: [
                    userLatLng,
                    L.latLng(lat, lng)
                ],
                routeWhileDragging: false,
                show: false
            }).addTo(map);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initIlocosProductsMap();

            document.getElementById('produceFilter').addEventListener('change', applyFilters);
            document.getElementById('municipalityFilter').addEventListener('change', applyFilters);
            document.getElementById('farmerFilter').addEventListener('input', applyFilters);
        });

        document.addEventListener('livewire:navigated', initIlocosProductsMap);
    </script>

</x-layouts.app>
