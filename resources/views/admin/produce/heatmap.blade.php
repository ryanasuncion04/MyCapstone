<x-layouts.adapp title="Produce Density Heatmap">

    <div class="p-6 space-y-4">
        <h1 class="text-2xl font-semibold">Produce Density Heatmap (Barangay Level)</h1>

        <div class="flex gap-4">
            <select id="productFilter" class="border rounded-lg p-2">
                <option value="">All Products</option>
                @foreach ($products as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                @endforeach
            </select>

            <select id="metricFilter" class="border rounded-lg p-2">
                <option value="quantity">By Quantity</option>
                <option value="revenue">By Revenue</option>
            </select>
        </div>

        <div id="heatmap" class="w-full h-[600px] rounded-xl border"></div>
    </div>

    <script>
        let map;
        let heatLayer;

        function initMap() {
            map = L.map('heatmap').setView([18.1647, 120.7116], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            loadHeatmap();
        }

        function loadHeatmap() {
            const product = document.getElementById('productFilter').value;
            const metric = document.getElementById('metricFilter').value;

            fetch(`{{ route('admin.produce.heatmap.data') }}?product=${product}&metric=${metric}`)
                .then(res => res.json())
                .then(apiData => {

                    fetch('{{ asset('maps/ilocos-norte.geojson') }}')
                        .then(res => res.json())
                        .then(geojson => {

                            const heatPoints = [];

                            apiData.forEach(row => {
                                geojson.features.forEach(feature => {

                                    const geoBarangay = feature.properties.Barangay.toLowerCase()
                                        .trim();
                                    const rowBarangay = row.barangay.toLowerCase().trim();

                                    if (geoBarangay === rowBarangay) {

                                        const center = L.geoJSON(feature)
                                            .getBounds()
                                            .getCenter();

                                        const intensity = Math.min(Number(row.value) / 100, 1);

                                        heatPoints.push([center.lat, center.lng, intensity]);
                                    }
                                });
                            });

                            if (heatLayer) map.removeLayer(heatLayer);

                            if (heatPoints.length) {
                                heatLayer = L.heatLayer(heatPoints, {
                                    radius: 35,
                                    blur: 30,
                                    maxZoom: 12,
                                }).addTo(map);
                            } else {
                                console.warn('No heat points to display!');
                            }

                            console.log('Heat points:', heatPoints);
                        });
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            document.getElementById('productFilter').addEventListener('change', loadHeatmap);
            document.getElementById('metricFilter').addEventListener('change', loadHeatmap);
        });
    </script>


</x-layouts.adapp>
