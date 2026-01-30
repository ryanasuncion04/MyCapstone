<x-layouts.adapp title="Admin Dashboard">

    <div class="p-6 space-y-6">

        <h1 class="text-2xl font-semibold">Farm Produce Visualization</h1>

        {{-- Filters --}}
        <div class="flex gap-4 items-center">
            <select id="productFilter"
                class="border rounded-lg p-2 text-sm">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                @endforeach
            </select>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-zinc-100">
                <h3 class="text-sm text-zinc-500">Top Municipality</h3>
                <p id="topMunicipality" class="text-l font-semibold text-green-600">—</p>
            </div>
            <div class="p-4 rounded-xl bg-zinc-100">
                <h3 class="text-sm text-zinc-500">Top Quantity</h3>
                <p id="topQuantity" class="text-l font-semibold text-green-600">—</p>
            </div>
            <div class="p-4 rounded-xl bg-zinc-100">
                <h3 class="text-sm text-zinc-500">Product</h3>
                <p id="currentProduct" class="text-l font-semibold text-green-600">All</p>
            </div>
        </div>

        {{-- Map --}}
        <div id="map"
            class="w-full h-[550px] rounded-xl border"></div>

    </div>

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

    <script>
        const map = L.map('map').setView([18.1647, 120.7116], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let geoLayer;
        let heatLayer;

        function getColor(qty) {
            return qty > 1000 ? '#7f1d1d' :
                   qty > 500  ? '#b91c1c' :
                   qty > 200  ? '#ef4444' :
                   qty > 50   ? '#fca5a5' :
                                '#fee2e2';
        }

        function loadMap(product = '') {
            fetch(`/admin/api/produce-map?product=${product}`)
                .then(res => res.json())
                .then(res => {

                    document.getElementById('currentProduct').innerText =
                        product || 'All';

                    if (res.top3.length) {
                        document.getElementById('topMunicipality').innerText =
                            res.top3[0].municipality;
                        document.getElementById('topQuantity').innerText =
                            res.top3[0].total_quantity;
                    }

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
                                return [
                                    geo.features.find(f => f.properties.Municipality === d.municipality)
                                        ?.geometry.coordinates[0][0][1],
                                    geo.features.find(f => f.properties.Municipality === d.municipality)
                                        ?.geometry.coordinates[0][0][0],
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

        document.getElementById('productFilter')
            .addEventListener('change', e => {
                loadMap(e.target.value);
            });

        loadMap();
    </script>

</x-layouts.adapp>
