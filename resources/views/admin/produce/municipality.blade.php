<x-layouts.adapp title="Municipality Performance">

    <div class="grid grid-cols-3 gap-6 p-6">

        {{-- MAP --}}
        <div class="col-span-2">
            <div id="map" class="h-[600px] rounded-xl border"></div>
        </div>

        {{-- SIDE PANEL --}}
        <div class="space-y-4">
            <h2 class="text-xl font-semibold" id="municipalityTitle">
                Click a Municipality
            </h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 border rounded-xl">
                    <p class="text-sm text-gray-500">Total Quantity</p>
                    <p class="text-xl font-bold" id="totalQuantity">—</p>
                </div>

                <div class="p-4 border rounded-xl">
                    <p class="text-sm text-gray-500">Total Revenue</p>
                    <p class="text-xl font-bold" id="totalRevenue">—</p>
                </div>

                <div class="p-4 border rounded-xl">
                    <p class="text-sm text-gray-500">Avg Price</p>
                    <p class="text-xl font-bold" id="avgPrice">—</p>
                </div>

                <div class="p-4 border rounded-xl">
                    <p class="text-sm text-gray-500">Farmers</p>
                    <p class="text-xl font-bold" id="farmerCount">—</p>
                </div>
            </div>

            <div>
                <p class="font-semibold">Top Products</p>
                <ul id="topProducts" class="list-disc pl-5 text-sm"></ul>
            </div>
        </div>

    </div>


    <script>
        let map;
        let municipalityLayer;
        const highlightedMunicipality = {};

        document.addEventListener('DOMContentLoaded', () => {

            map = L.map('map').setView([18.1647, 120.7116], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            loadMunicipalities();
        });

        function loadMunicipalities() {
            fetch('/maps/ilocos-norte.geojson')
                .then(res => res.json())
                .then(geojson => {

                    // Group features by Municipality
                    const municipalities = {};
                    geojson.features.forEach(f => {
                        const name = f.properties.Municipality;
                        if (!municipalities[name]) municipalities[name] = [];
                        municipalities[name].push(f);
                    });

                    municipalityLayer = L.layerGroup();

                    Object.keys(municipalities).forEach(name => {

                        const group = L.geoJSON(municipalities[name], {
                            style: {
                                fillColor: '#60a5fa',
                                weight: 1,
                                color: '#1e3a8a',
                                fillOpacity: 0.5
                            },
                            onEachFeature: (feature, layer) => {
                                layer.bindTooltip(name);
                            }
                        });

                        group.addTo(municipalityLayer);

                        group.eachLayer(layer => {
                            layer.on('click', () => {
                                highlightMunicipality(name);
                                loadMunicipalityData(name);
                            });
                        });

                        highlightedMunicipality[name] = group;
                    });

                    municipalityLayer.addTo(map);
                });
        }

        function highlightMunicipality(name) {

            // Reset all
            Object.values(highlightedMunicipality).forEach(group => {
                group.setStyle({ fillColor: '#60a5fa', fillOpacity: 0.5 });
            });

            // Highlight clicked municipality
            highlightedMunicipality[name].setStyle({ fillColor: '#f97316', fillOpacity: 0.7 });
        }

        function loadMunicipalityData(municipality) {

            document.getElementById('municipalityTitle').innerText = municipality;

            fetch(`/admin/municipality-map/${encodeURIComponent(municipality)}`)
                .then(res => res.json())
                .then(data => {

                    document.getElementById('totalQuantity').innerText =
                        Number(data.summary.total_quantity).toLocaleString();

                    document.getElementById('totalRevenue').innerText =
                        '₱' + Number(data.summary.total_revenue).toLocaleString();

                    document.getElementById('avgPrice').innerText =
                        '₱' + Number(data.summary.avg_price).toFixed(2);

                    document.getElementById('farmerCount').innerText =
                        data.summary.farmer_count;

                    const list = document.getElementById('topProducts');
                    list.innerHTML = '';

                    data.topProducts.forEach(p => {
                        list.innerHTML += `<li>${p.product} (${p.total_quantity})</li>`;
                    });
                });
        }
    </script>

</x-layouts.adapp>
