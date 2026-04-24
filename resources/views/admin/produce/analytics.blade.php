<x-layouts.adapp title="Produce Analytics">

    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-bold">Farm Produce Analytics</h1>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-4">
            <select id="product" class="border rounded-lg p-2">
                <option value="">All Products</option>
                @foreach ($products as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                @endforeach
            </select>

            <select id="municipality" class="border rounded-lg p-2">
                <option value="">All Municipalities</option>
                @foreach ($municipalities as $municipality)
                    <option value="{{ $municipality }}">{{ $municipality }}</option>
                @endforeach
            </select>

            <select id="range" class="border rounded-lg p-2">
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
                <option value="daily">Daily</option>
            </select>

            <select id="limit" class="border rounded-lg p-2">
                <option value="5">Top 5</option>
                <option value="10" selected>Top 10</option>
                <option value="20">Top 20</option>
            </select>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="p-4 border rounded-xl bg-white">
                <h2 class="font-semibold mb-3">Produce Trend</h2>
                <canvas id="trendChart"></canvas>
            </div>

            <div class="p-4 border rounded-xl bg-white">
                <h2 class="font-semibold mb-3">Product Distribution</h2>
                <canvas id="productChart"></canvas>
            </div>

            <div class="p-4 border rounded-xl bg-white">
                <h2 class="font-semibold mb-3">Avg Price</h2>
                <canvas id="avgPriceChart"></canvas>
            </div>

            <div class="p-4 border rounded-xl bg-white">
                <h2 class="font-semibold mb-3">Top Farmers</h2>
                <canvas id="yieldChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const productEl = document.getElementById('product');
        const municipalityEl = document.getElementById('municipality');
        const rangeEl = document.getElementById('range');
        const limitEl = document.getElementById('limit');

        let charts = {};

        function loadAnalytics() {
            const product = productEl.value;
            const municipality = municipalityEl.value;
            const range = rangeEl.value;
            const limit = limitEl.value;

            fetch(
                    `{{ route('admin.produce.analytics.data') }}?product=${product}&municipality=${municipality}&range=${range}&limit=${limit}`)
                .then(res => res.json())
                .then(data => {
                    renderTrend(data.trends);
                    renderProduct(data.productDistribution);
                    renderAvgPrice(data.avgPrice);
                    renderYield(data.yieldPerFarmer);
                });
        }

        function renderTrend(data) {
            charts.trend?.destroy();

            charts.trend = new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: data.map(d => d.period),
                    datasets: [{
                        label: 'Total Quantity',
                        data: data.map(d => d.total_quantity),
                        borderWidth: 2,
                        tension: 0.4
                    }]
                }
            });
        }

        function renderProduct(data) {
            charts.product?.destroy();

            charts.product = new Chart(document.getElementById('productChart'), {
                type: 'bar',
                data: {
                    labels: data.map(d => d.product),
                    datasets: [{
                        label: 'Quantity',
                        data: data.map(d => d.total)
                    }]
                }
            });
        }

        function renderAvgPrice(data) {
            charts.avg?.destroy();

            charts.avg = new Chart(document.getElementById('avgPriceChart'), {
                type: 'bar',
                data: {
                    labels: data.map(d => d.municipality),
                    datasets: [{
                        label: 'Avg Price',
                        data: data.map(d => d.avg_price)
                    }]
                }
            });
        }

        function renderYield(data) {
            charts.yield?.destroy();

            charts.yield = new Chart(document.getElementById('yieldChart'), {
                type: 'bar',
                data: {
                    labels: data.map(d => d.name),
                    datasets: [{
                        label: 'Yield',
                        data: data.map(d => d.total_quantity)
                    }]
                }
            });
        }

        productEl.addEventListener('change', loadAnalytics);
        municipalityEl.addEventListener('change', loadAnalytics);
        rangeEl.addEventListener('change', loadAnalytics);
        limitEl.addEventListener('change', loadAnalytics);

        loadAnalytics();
    </script>

</x-layouts.adapp>
