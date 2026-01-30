<x-layouts.app title="Produce Map Dashboard">

    <div class="p-6 space-y-6">

        <h1 class="text-2xl font-semibold">
            Ilocos Norte Produce Visualization
        </h1>

        <!-- Filter -->
        <div class="flex gap-4 items-center">
            <select id="produceFilter" class="border rounded-lg p-2 text-sm w-64">
                <option value="">All Produce</option>
                @foreach ($products as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                @endforeach
            </select>
        </div>

        <!-- Map -->
        <div id="map" class="w-full h-[520px] rounded-xl border border-zinc-300">
        </div>

        <!-- Top 3 -->
        <div>
            <h2 class="text-lg font-semibold mb-2">
                Top 3 Producing Municipalities
            </h2>
            <ul id="top3" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-green-600">
            </ul>
        </div>

    </div>

    <script>
        const map = L.map('map').setView([18.1647, 120.7116], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let geoLayer = null;
        let heatLayer = null;

        /**
         * Choropleth color scale
         */
        function getColor(qty) {
            return qty > 1000 ? '#7f1d1d' :
                qty > 500 ? '#b91c1c' :
                qty > 200 ? '#ef4444' :
                qty > 50 ? '#fca5a5' :
                '#fee2e2';
        }

        async function loadMap(product = '') {

            // API CALL (YOUR ROUTE)
            const response = await fetch(
                `{{ route('user.produce.map') }}?product=${product}`
            );

            const result = await response.json();

            // Municipality → Quantity lookup
            const totals = {};
            result.data.forEach(row => {
                totals[row.municipality] = Number(row.total_quantity);
            });

            if (geoLayer) map.removeLayer(geoLayer);
            if (heatLayer) map.removeLayer(heatLayer);

            const geojson = await fetch('/maps/ilocos-norte.geojson')
                .then(r => r.json());

            const heatPoints = [];

            geoLayer = L.geoJSON(geojson, {
                style: feature => {
                    const name = feature.properties.Municipality;
                    const total = totals[name] ?? 0;

                    return {
                        color: '#7f1d1d',
                        weight: 1,
                        fillColor: getColor(total),
                        fillOpacity: 0.7
                    };
                },
                onEachFeature: (feature, layer) => {
                    const name = feature.properties.Municipality;
                    const total = totals[name] ?? 0;

                    layer.bindTooltip(`
                <strong>${name}</strong><br>
                Total Produce: ${total}
            `);

                    // Heatmap point
                    if (total > 0) {
                        const center = layer.getBounds().getCenter();
                        heatPoints.push([
                            center.lat,
                            center.lng,
                            total / 100
                        ]);
                    }
                }
            }).addTo(map);

            heatLayer = L.heatLayer(heatPoints, {
                radius: 35,
                blur: 25,
                maxZoom: 10
            }).addTo(map);

            // Top 3
            const list = document.getElementById('top3');
            list.innerHTML = '';

            result.top3.forEach((item, index) => {
                list.innerHTML += `
            <li class="p-4 border rounded-lg bg-white">
                <strong>#${index + 1} ${item.municipality}</strong><br>
                Quantity: ${item.total_quantity}
            </li>
        `;
            });
        }

        // Filter change
        document.getElementById('produceFilter')
            .addEventListener('change', e => {
                loadMap(e.target.value);
            });

        // Initial load
        loadMap();
    </script>

</x-layouts.app>
