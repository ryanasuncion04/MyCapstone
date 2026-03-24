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
        <div wire:ignore id="map"
            class="w-full h-[80vh] rounded-xl border border-zinc-300 dark:border-zinc-700">
        </div>
    </div>

    <script>
        const produces = @json($produces);

        let map;
        let markerLayer;
        let geojsonData;

        function initIlocosProductsMap() {
            const el = document.getElementById('map');
            if (!el || el.dataset.loaded) return;
            el.dataset.loaded = true;

            map = L.map(el).setView([18.1647, 120.7116], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            markerLayer = L.layerGroup().addTo(map);

            /**
             * LOAD GEOJSON
             */
            fetch('/maps/ilocos-norte.geojson')
                .then(res => res.json())
                .then(data => {

                    geojsonData = data;

                    // Draw boundaries
                    L.geoJSON(data, {
                        style: {
                            color: '#2563eb',
                            weight: 2,
                            fillOpacity: 0.2
                        }
                    }).addTo(map);

                    // ✅ Populate municipality dropdown
                    populateMunicipalities(data);

                    // Initial markers
                    applyFilters();

                    setTimeout(() => map.invalidateSize(), 300);
                });
        }

        /**
         * POPULATE MUNICIPALITY DROPDOWN
         */
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

        /**
         * APPLY BOTH FILTERS
         */
        function applyFilters() {
            const selectedProduct = document.getElementById('produceFilter').value;
            const selectedMunicipality = document.getElementById('municipalityFilter').value;

            let filtered = produces;

            // Filter by product
            if (selectedProduct) {
                filtered = filtered.filter(p =>
                    p.product.toLowerCase() === selectedProduct.toLowerCase()
                );
            }

            // Filter by municipality
            if (selectedMunicipality) {
                filtered = filtered.filter(p =>
                    p.farmer &&
                    p.farmer.municipality === selectedMunicipality
                );
            }

            renderMarkers(filtered);
        }

        /**
         * RENDER MARKERS
         */
        function renderMarkers(filteredProduces) {

            markerLayer.clearLayers();

            const farmersMap = {};

            filteredProduces.forEach(p => {
                if (!p.farmer || !p.farmer.latitude || !p.farmer.longitude) return;

                if (!farmersMap[p.farmer.id]) {
                    farmersMap[p.farmer.id] = {
                        farmer: p.farmer,
                        produces: []
                    };
                }

                farmersMap[p.farmer.id].produces.push(p);
            });

            Object.values(farmersMap).forEach(entry => {
                const farmer = entry.farmer;
                const farmerProduces = entry.produces;

                let rows = '';

                farmerProduces.forEach(prod => {
                    const preorderUrl = `/customer/preorders/create/${prod.id}`;

                    rows += `
                        <tr class="border-t">
                            <td class="px-2 py-1">${prod.product}</td>
                            <td class="px-2 py-1 text-center">${prod.quantity}</td>
                            <td class="px-2 py-1 text-right">₱${prod.price}</td>
                            <td class="px-2 py-1 text-center">
                                <a href="${preorderUrl}"
                                   class="bg-green-600 text-white text-xs px-2 py-1 rounded hover:bg-green-700">
                                   Preorder
                                </a>
                            </td>
                        </tr>
                    `;
                });

                const farmerImage = farmer.image ?
                    `/storage/${farmer.image}` :
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(farmer.name)}&background=2563eb&color=fff`;

                const managerId = farmerProduces[0].user_id;

                const popup = `
                    <div class="w-[300px] text-sm relative">

                        <a href="javascript:void(0)"
                            onclick="startChat(${managerId})"
                            class="absolute top-1 right-1 text-blue-600 hover:text-blue-800">
                            💬
                        </a>

                        <div class="flex justify-center mb-2">
                            <img src="${farmerImage}"
                                class="w-20 h-20 rounded-full object-cover border">
                        </div>

                        <div class="text-center mb-2">
                            <strong>${farmer.name}</strong><br>
                            <span class="text-xs text-gray-600">
                                ${farmer.barangay}, ${farmer.municipality}
                            </span>
                        </div>

                        <div class="max-h-36 overflow-y-auto border rounded">
                            <table class="w-full text-xs">
                                <tbody>${rows}</tbody>
                            </table>
                        </div>

                    </div>
                `;

                L.marker([farmer.latitude, farmer.longitude])
                    .addTo(markerLayer)
                    .bindPopup(popup, { maxWidth: 340 });
            });
        }

        /**
         * EVENTS
         */
        document.addEventListener('DOMContentLoaded', () => {

            initIlocosProductsMap();

            document.getElementById('produceFilter')
                .addEventListener('change', applyFilters);

            document.getElementById('municipalityFilter')
                .addEventListener('change', applyFilters);
        });

        document.addEventListener('livewire:navigated', initIlocosProductsMap);
    </script>

    <script>
        function startChat(managerId) {
            fetch("{{ route('chat.start') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ user_id: managerId })
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                }
            });
        }
    </script>

</x-layouts.app>
