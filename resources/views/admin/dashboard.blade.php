<x-layouts.adapp title="Admin Dashboard">

    <div class="space-y-6 p-6">

        <!-- Header Section -->
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
                Farm Produce Analytics
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400">
                Visualize and analyze agricultural produce distribution across Ilocos Norte
            </p>
        </div>

        <!-- Filter Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-primary-200 dark:border-primary-900 p-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Product Filter -->
                <div>
                    <label for="productFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Filter by Product
                    </label>
                    <select id="productFilter" class="w-full px-4 py-2.5 rounded-lg border border-primary-300 dark:border-primary-700 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        <option value="">All Products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Top N Filter -->
                <div>
                    <label for="topFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Show Top Municipalities
                    </label>
                    <select id="topFilter" class="w-full px-4 py-2.5 rounded-lg border border-primary-300 dark:border-primary-700 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        <option value="3">Top 3</option>
                        <option value="5">Top 5</option>
                        <option value="10">Top 10</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl p-6 border border-primary-200 dark:border-primary-900">
                <h3 class="text-sm font-medium text-primary-700 dark:text-primary-300 mb-2">Top Municipality</h3>
                <p id="topMunicipality" class="text-3xl font-bold text-primary-700 dark:text-primary-400">—</p>
            </div>
            <div class="bg-gradient-to-br from-harvest-50 to-harvest-100 dark:from-harvest-900/20 dark:to-harvest-800/20 rounded-xl p-6 border border-harvest-200 dark:border-harvest-900">
                <h3 class="text-sm font-medium text-harvest-700 dark:text-harvest-300 mb-2">Total Quantity (kg)</h3>
                <p id="topQuantity" class="text-3xl font-bold text-harvest-700 dark:text-harvest-400">—</p>
            </div>
            <div class="bg-gradient-to-br from-earth-50 to-earth-100 dark:from-earth-900/20 dark:to-earth-800/20 rounded-xl p-6 border border-earth-200 dark:border-earth-900">
                <h3 class="text-sm font-medium text-earth-700 dark:text-earth-300 mb-2">Selected Product</h3>
                <p id="currentProduct" class="text-3xl font-bold text-earth-700 dark:text-earth-400">All</p>
            </div>
        </div>

        <!-- Top Municipalities List & Map -->
        <div class="grid lg:grid-cols-4 gap-4">
            <!-- Top List -->
            <div class="lg:col-span-1 bg-white dark:bg-zinc-800 rounded-xl border border-primary-200 dark:border-primary-900 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3 px-2">Top Municipalities</h3>
                <ul id="topList" class="text-sm space-y-2 max-h-96 overflow-y-auto">
                    <li class="px-3 py-2 text-zinc-500 text-center">Loading...</li>
                </ul>
            </div>

            <!-- Map -->
            <div class="lg:col-span-3">
                <div id="map" class="w-full h-[550px] rounded-xl border border-primary-200 dark:border-primary-900 shadow-md overflow-hidden"></div>
            </div>
        </div>
    </div>

    <script>
        const map = L.map('map').setView([18.1647, 120.7116], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let geoLayer;
        let heatLayer;

        function getColor(qty) {
            return qty > 1000 ? '#78350f' :
                qty > 500 ? '#b45309' :
                qty > 200 ? '#d97706' :
                qty > 50 ? '#fcd34d' :
                '#fef3c7';
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
                            (res.top[0].total_quantity).toLocaleString();
                    }

                    // Top List
                    const list = document.getElementById('topList');
                    list.innerHTML = '';

                    res.top.forEach((item, i) => {
                        list.innerHTML += `
                        <li class="px-3 py-2 rounded-lg bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-primary-700 dark:text-primary-400">${i + 1}.</span>
                                <span class="text-zinc-700 dark:text-zinc-300">${item.municipality}</span>
                            </div>
                            <div class="text-xs text-zinc-500 mt-1">
                                ${(item.total_quantity).toLocaleString()} kg
                            </div>
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
                                        weight: 2,
                                        color: '#15803d',
                                        fillOpacity: 0.7
                                    };
                                },
                                onEachFeature: (f, layer) => {
                                    const found = res.data.find(
                                        d => d.municipality === f.properties.Municipality
                                    );

                                    layer.bindTooltip(`
                                    <div class="text-sm">
                                        <strong class="text-primary-700">${f.properties.Municipality}</strong><br>
                                        <span class="text-zinc-600">Total Produce: ${found ? (found.total_quantity).toLocaleString() : 0} kg</span>
                                    </div>
                                `, {className: 'bg-white dark:bg-zinc-800'});
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
                                    d.total_quantity / 1000
                                ];
                            }).filter(Boolean);

                            if (heatPoints.length > 0) {
                                heatLayer = L.heatLayer(heatPoints, {
                                    radius: 30,
                                    blur: 20,
                                    maxZoom: 10,
                                    gradient: {0.0: '#fef3c7', 0.5: '#fcd34d', 1.0: '#ca8a04'}
                                }).addTo(map);
                            }
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

</x-layouts.adapp>
