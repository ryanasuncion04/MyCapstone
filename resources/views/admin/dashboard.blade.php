<x-layouts.adapp title="Admin Dashboard">

    ```
    <div class="p-6 space-y-6">

        <h1 class="text-2xl font-semibold">Farm Produce Visualization</h1>

        {{-- Filters --}}
        <div class="flex gap-4 items-center">
            <!-- Product Filter -->
            <select id="productFilter" class="border rounded-lg p-2 text-sm">
                <option value="">All Products</option>
                @foreach ($products as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                @endforeach
            </select>

            <!-- Top N Filter -->
            <select id="topFilter" class="border rounded-lg p-2 text-sm">
                <option value="3">Top 3</option>
                <option value="5">Top 5</option>
                <option value="10">Top 10</option>
            </select>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-zinc-100">
                <h3 class="text-sm text-zinc-500">Top Municipality</h3>
                <p id="topMunicipality" class="text-lg font-semibold text-green-600">—</p>
            </div>
            <div class="p-4 rounded-xl bg-zinc-100">
                <h3 class="text-sm text-zinc-500">Top Quantity</h3>
                <p id="topQuantity" class="text-lg font-semibold text-green-600">—</p>
            </div>
            <div class="p-4 rounded-xl bg-zinc-100">
                <h3 class="text-sm text-zinc-500">Product</h3>
                <p id="currentProduct" class="text-lg font-semibold text-green-600">All</p>
            </div>
        </div>

        {{-- Top List --}}
        <div class="p-4 bg-white rounded-xl border">
            <h3 class="text-sm text-zinc-500 mb-2">Top Municipalities</h3>
            <ul id="topList" class="text-sm space-y-1"></ul>
        </div>

        {{-- Map --}}
        <div id="map" class="w-full h-[550px] rounded-xl border"></div>

    </div>

    <script>
        const map = L.map('map').setView([18.1647, 120.7116], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let geoLayer;
        let heatLayer;

        function getColor(qty) {
            return qty > 1000 ? '#7f1d1d' :
                qty > 500 ? '#b91c1c' :
                qty > 200 ? '#ef4444' :
                qty > 50 ? '#fca5a5' :
                '#fee2e2';
        }

        function loadMap(product = '', limit = 3) {
            fetch(`/admin/api/produce-map?product=${product}&limit=${limit}`)
                .then(res => res.json())
                .then(res => {

                    document.getElementById('currentProduct').innerText =
                        product || 'All';

                    // KPI
                    if (res.top.length) {
                        document.getElementById('topMunicipality').innerText =
                            res.top[0].municipality;

                        document.getElementById('topQuantity').innerText =
                            res.top[0].total_quantity;
                    }

                    // Top List
                    const list = document.getElementById('topList');
                    list.innerHTML = '';

                    res.top.forEach((item, i) => {
                        list.innerHTML += `
                        <li>
                            <span class="font-semibold">${i + 1}.</span>
                            ${item.municipality}
                            <span class="text-zinc-500">(${item.total_quantity})</span>
                        </li>
                    `;
                    });

                    if (geoLayer) geoLayer.remove();
                    if (heatLayer) heatLayer.remove();

                    fetch('/maps/ilocos-norte.geojson')
                        .then(r => r.json())
                        .then(geo => {

                            geoLayer = L.geoJSON(geo, {
                                style: f => {
                                    const found = res.data.find(
                                        d => d.municipality === f.properties.Municipality
                                    );
                                    const qty = found ? found.total_quantity : 0;

                                    return {
                                        fillColor: getColor(qty),
                                        weight: 1,
                                        fillOpacity: 0.6
                                    };
                                },
                                onEachFeature: (f, layer) => {
                                    const found = res.data.find(
                                        d => d.municipality === f.properties.Municipality
                                    );

                                    layer.bindTooltip(`
                                    <strong>${f.properties.Municipality}</strong><br>
                                    Total Produce: ${found ? found.total_quantity : 0}
                                `);
                                }
                            }).addTo(map);

                            const heatPoints = res.data.map(d => {
                                const match = geo.features.find(
                                    f => f.properties.Municipality === d.municipality
                                );

                                if (!match) return null;

                                return [
                                    match.geometry.coordinates[0][0][1],
                                    match.geometry.coordinates[0][0][0],
                                    d.total_quantity
                                ];
                            }).filter(Boolean);

                            heatLayer = L.heatLayer(heatPoints, {
                                radius: 25,
                                blur: 15,
                                maxZoom: 10
                            }).addTo(map);
                        });
                });
        }

        const productFilter = document.getElementById('productFilter');
        const topFilter = document.getElementById('topFilter');

        function reload() {
            loadMap(productFilter.value, topFilter.value);
        }

        productFilter.addEventListener('change', reload);
        topFilter.addEventListener('change', reload);

        // Initial load
        loadMap();
    </script>
    ```

</x-layouts.adapp>
