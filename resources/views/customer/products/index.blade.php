<x-layouts.app :title="__('Farm Produces Ilocos Norte')">

    <div class="p-6 space-y-4">
        <h1 class="text-2xl font-semibold">
            All Available Farm Produces
        </h1>

        <div wire:ignore id="map" class="w-full h-[80vh] rounded-xl border border-zinc-300 dark:border-zinc-700">
        </div>
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
                            const preorderUrl = `/customer/preorders/create/${prod.id}`;

                            rows += `
                                <tr class="border-t">
                                    <td class="px-2 py-1">${prod.product}</td>
                                    <td class="px-2 py-1 text-center">${prod.quantity}</td>
                                    <td class="px-2 py-1 text-right">₱${prod.price}</td>
                                    <td class="px-2 py-1 text-center">
                                        <a
                                            href="${preorderUrl}"
                                            class="bg-green-600 text-white text-xs px-2 py-1 rounded hover:bg-green-700"
                                        >
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

                                <!-- Message Icon -->
                                <a href="javascript:void(0)"
                                    onclick="startChat(${managerId})"
                                    class="absolute top-1 right-1 text-blue-600 hover:text-blue-800"
                                    title="Message seller">
                                    💬
                                </a>

                                <!-- Farmer Image -->
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
                                        <thead class="bg-gray-100 sticky top-0">
                                            <tr>
                                                <th class="px-2 py-1">Product</th>
                                                <th class="px-2 py-1">Qty</th>
                                                <th class="px-2 py-1">Price</th>
                                                <th class="px-2 py-1">Order</th>
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
                            .bindPopup(popup, {
                                maxWidth: 340
                            });
                    });

                    setTimeout(() => map.invalidateSize(), 300);
                });
        }

        document.addEventListener('DOMContentLoaded', initIlocosProductsMap);
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
                    body: JSON.stringify({
                        user_id: managerId
                    })
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    }
                });
        }
    </script>

</x-layouts.app>
