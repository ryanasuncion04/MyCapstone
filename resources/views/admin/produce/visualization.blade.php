<x-layouts.adapp title="Produce Visualization">

    <div class="space-y-6 p-6">

        <h1 class="text-2xl font-semibold">Farm Produce Visualization</h1>

        {{-- FILTER --}}
        <select
            id="productFilter"
            class="border rounded-lg p-2 w-64"
        >
            <option value="">All Products</option>
            @foreach($products as $product)
                <option value="{{ $product }}">{{ $product }}</option>
            @endforeach
        </select>

        {{-- MAP --}}
        <div
            id="map"
            class="h-[450px] rounded-xl border"
        ></div>

        {{-- TOP 3 --}}
        <div>
            <h2 class="text-lg font-semibold mb-2">Top 3 Producing Municipalities</h2>
            <ul id="top3" class="list-disc ml-5"></ul>
        </div>

        {{-- TREND CHART --}}
        <div>
            <h2 class="text-lg font-semibold mb-2">
                Municipality Production Trend
            </h2>
            <canvas id="municipalityTrendChart" height="120"></canvas>
        </div>

    </div>

    {{-- LIBS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let map;
        let geoLayer;
        let charts = {};

        const routeTemplate =
            "{{ route('admin.produce.visualization.municipality', ['municipality' => '__MUNICIPALITY__']) }}";

        function getColor(qty) {
            return qty > 1000 ? '#7f1d1d' :
                   qty > 500  ? '#b91c1c' :
                   qty > 200  ? '#ef4444' :
                   qty > 50   ? '#fca5a5' :
                                '#fee2e2';
        }

        function initMap() {
            map = L.map('map').setView([18.1647, 120.7116], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            loadMapData();
        }

        function loadMapData() {
            const product = document.getElementById('productFilter').value;

            fetch(`{{ route('admin.produce.visualization.data') }}?product=${product}`)
                .then(res => res.json())
                .then(res => {

                    const quantities = {};
                    res.data.forEach(d => quantities[d.municipality] = d.total_quantity);

                    fetch('/maps/ilocos-norte.geojson')
                        .then(res => res.json())
                        .then(geo => {

                            geoLayer?.remove();

                            geoLayer = L.geoJSON(geo, {
                                style: f => ({
                                    fillColor: getColor(
                                        quantities[f.properties.Municipality] || 0
                                    ),
                                    weight: 1,
                                    fillOpacity: 0.6
                                }),
                                onEachFeature: (f, layer) => {
                                    layer.bindTooltip(
                                        `${f.properties.Municipality}<br>
                                         Qty: ${quantities[f.properties.Municipality] || 0}`
                                    );

                                    layer.on('click', () => {
                                        loadMunicipalityTrend(
                                            f.properties.Municipality
                                        );
                                    });
                                }
                            }).addTo(map);
                        });

                    const top3 = document.getElementById('top3');
                    top3.innerHTML = '';
                    res.top3.forEach(t => {
                        top3.innerHTML +=
                            `<li>${t.municipality} — ${t.total_quantity}</li>`;
                    });
                });
        }

        function loadMunicipalityTrend(municipality) {
            const url = routeTemplate.replace(
                '__MUNICIPALITY__',
                encodeURIComponent(municipality)
            );

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    charts.trend?.destroy();

                    charts.trend = new Chart(
                        document.getElementById('municipalityTrendChart'),
                        {
                            type: 'line',
                            data: {
                                labels: data.map(d => d.period),
                                datasets: [{
                                    label: municipality,
                                    data: data.map(d => d.total_quantity),
                                    tension: 0.3
                                }]
                            }
                        }
                    );
                });
        }

        document.getElementById('productFilter')
            .addEventListener('change', loadMapData);

        document.addEventListener('DOMContentLoaded', initMap);
    </script>

</x-layouts.adapp>
