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

            {{-- PRODUCT FILTER --}}
            <div>
                <label class="text-sm text-gray-600">Filter by Product</label>
                <select id="productFilter" class="w-full border rounded-lg p-2 text-sm mt-1">
                    <option value="">All Products</option>
                </select>
            </div>

            {{-- SUMMARY --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 border rounded-xl">
                    <p class="text-sm text-gray-500">Total Quantity</p>
                    <p class="text-xl font-bold" id="totalQuantity">—</p>
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

            {{-- PRODUCTS --}}
            <div>
                <p class="font-semibold">Available Products</p>
                <ul id="productsList" class="list-disc pl-5 text-sm"></ul>
            </div>

            {{-- TOP FARMERS --}}
            <div>
                <p class="font-semibold">Top Farmer per Product</p>
                <ul id="topFarmers" class="list-disc pl-5 text-sm"></ul>
            </div>

        </div>

    </div>

    <script>
        let map;
        let municipalityLayer;
        const highlightedMunicipality = {};
        let selectedProduct = '';

        document.addEventListener('DOMContentLoaded', () => {

            map = L.map('map').setView([18.1647, 120.7116], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            loadMunicipalities();

            document.getElementById('productFilter').addEventListener('change', function() {
                selectedProduct = this.value;

                const municipality = document.getElementById('municipalityTitle').innerText;

                if (municipality !== 'Click a Municipality') {
                    loadMunicipalityData(municipality);
                }
            });
        });

        function loadMunicipalities() {
            fetch('/maps/ilocos-norte.geojson')
                .then(res => res.json())
                .then(geojson => {

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
            Object.values(highlightedMunicipality).forEach(group => {
                group.setStyle({
                    fillColor: '#60a5fa',
                    fillOpacity: 0.5
                });
            });

            highlightedMunicipality[name].setStyle({
                fillColor: '#f97316',
                fillOpacity: 0.7
            });
        }

        function loadMunicipalityData(municipality) {

            document.getElementById('municipalityTitle').innerText = municipality;

            fetch(`/admin/municipality-map/${encodeURIComponent(municipality)}?product=${selectedProduct}`)
                .then(res => res.json())
                .then(data => {

                    const summary = data.summary || {};

                    document.getElementById('totalQuantity').innerText =
                        Number(summary.total_quantity || 0).toLocaleString();

                    document.getElementById('avgPrice').innerText =
                        '₱' + Number(summary.avg_price || 0).toFixed(2);

                    document.getElementById('farmerCount').innerText =
                        summary.farmer_count || 0;

                    // Populate dropdown once
                    const select = document.getElementById('productFilter');
                    if (select.options.length === 1) {
                        data.products.forEach(p => {
                            select.innerHTML += `<option value="${p.product}">${p.product}</option>`;
                        });
                    }

                    // PRODUCTS
                    const productsList = document.getElementById('productsList');
                    productsList.innerHTML = '';

                    data.products.forEach(p => {
                        productsList.innerHTML += `
                    <li>
                        ${p.product} — ₱${Number(p.avg_price).toFixed(2)}
                        (Qty: ${p.total_quantity})
                    </li>
                `;
                    });

                    // TOP FARMERS
                    const farmersList = document.getElementById('topFarmers');
                    farmersList.innerHTML = '';

                    data.topFarmersPerProduct.forEach(f => {
                        farmersList.innerHTML += `
                    <li>
                        ${f.product}: ${f.name} (${f.total_quantity})
                    </li>
                `;
                    });

                });
        }
    </script>

</x-layouts.adapp>
