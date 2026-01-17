<x-layouts.app :title="__('Farm Produces Ilocos Norte')">

    <div class="p-6 space-y-4">
        <h1 class="text-2xl font-semibold">
            All Available Farm Produces
        </h1>

        <div
            wire:ignore
            id="map"
            class="w-full h-[80vh] rounded-xl border border-zinc-300 dark:border-zinc-700"
        ></div>
    </div>

    <script>
        const produces = @json($produces);

        function initIlocosProductsMap() {
            const el = document.getElementById('map');
            if (!el || el.dataset.loaded) return;
            el.dataset.loaded = true;

            /**
             * INITIAL MAP VIEW (WHOLE ILOCOS NORTE)
             */
            const map = L.map(el).setView([18.1647, 120.7116], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            /**
             * DRAW FULL ILOCOS NORTE MAP
             */
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

                    /**
                     * GROUP PRODUCES BY FARMER
                     */
                    const farmersMap = {};

                    produces.forEach(p => {
                        if (!p.farmer || !p.farmer.latitude || !p.farmer.longitude) return;

                        if (!farmersMap[p.farmer.id]) {
                            farmersMap[p.farmer.id] = {
                                farmer: p.farmer,
                                produces: []
                            };
                        }

                        farmersMap[p.farmer.id].produces.push(p);
                    });

                    /**
                     * ONE MARKER PER FARMER
                     */
                    Object.values(farmersMap).forEach(entry => {
                        const farmer = entry.farmer;
                        const farmerProduces = entry.produces;

                        let rows = '';

                        farmerProduces.forEach(prod => {
                            rows += `
                                <tr class="border-t">
                                    <td class="px-2 py-1">${prod.product}</td>
                                    <td class="px-2 py-1 text-center">${prod.quantity}</td>
                                    <td class="px-2 py-1 text-right">₱${prod.price}</td>
                                </tr>
                            `;
                        });

                        const farmerImage = farmer.image
                            ? `/storage/${farmer.image}`
                            : `https://ui-avatars.com/api/?name=${encodeURIComponent(farmer.name)}&background=2563eb&color=fff`;

                        const popup = `
                            <div class="w-[270px] text-sm">

                                <!-- Farmer Image -->
                                <div class="flex justify-center mb-2">
                                    <img
                                        src="${farmerImage}"
                                        class="w-20 h-20 rounded-full object-cover border"
                                    >
                                </div>

                                <!-- Farmer Info -->
                                <div class="text-center mb-2">
                                    <strong class="block">${farmer.name}</strong>
                                    <span class="text-xs text-gray-600">
                                        ${farmer.barangay}, ${farmer.municipality}
                                    </span><br>
                                    <span class="text-xs">${farmer.contact ?? ''}</span>
                                </div>

                                <!-- Products Table -->
                                <div class="max-h-40 overflow-y-auto border rounded">
                                    <table class="w-full text-xs border-collapse">
                                        <thead class="bg-gray-100 sticky top-0">
                                            <tr>
                                                <th class="px-2 py-1 text-left">Product</th>
                                                <th class="px-2 py-1 text-center">Qty</th>
                                                <th class="px-2 py-1 text-right">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${rows}
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        `;

                        L.marker([farmer.latitude, farmer.longitude])
                            .addTo(map)
                            .bindPopup(popup, { maxWidth: 320 });
                    });

                    setTimeout(() => map.invalidateSize(), 300);
                });
        }

        document.addEventListener('DOMContentLoaded', initIlocosProductsMap);
        document.addEventListener('livewire:navigated', initIlocosProductsMap);
    </script>

</x-layouts.app>
