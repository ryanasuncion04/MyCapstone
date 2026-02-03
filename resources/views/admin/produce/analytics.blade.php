<x-layouts.adapp title="Produce Analytics">

    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-bold">📊 Farm Produce Analytics</h1>

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
        </div>

        {{-- Analytics Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Produce Trend --}}
            <div class="p-4 border rounded-xl shadow-sm bg-white">
                <h2 class="font-semibold mb-3">📈 Produce Trend Over Time</h2>
                <canvas id="trendChart"></canvas>
            </div>

            {{-- Product Distribution --}}
            <div class="p-4 border rounded-xl shadow-sm bg-white">
                <h2 class="font-semibold mb-3">📦 Product Distribution</h2>
                <canvas id="productChart"></canvas>
            </div>

            {{-- Average Price --}}
            <div class="p-4 border rounded-xl shadow-sm bg-white">
                <h2 class="font-semibold mb-3">💰 Average Price per Municipality</h2>
                <canvas id="avgPriceChart"></canvas>
            </div>

            {{-- Yield per Farmer --}}
            <div class="p-4 border rounded-xl shadow-sm bg-white">
                <h2 class="font-semibold mb-3">🏆 Top Farmers by Yield</h2>
                <canvas id="yieldChart"></canvas>
            </div>

        </div>
    </div>

   

    <script>
        const productEl = document.getElementById('product');
        const municipalityEl = document.getElementById('municipality');
        const rangeEl = document.getElementById('range');

        let charts = {};

        function loadAnalytics() {
            const product = productEl.value;
            const municipality = municipalityEl.value;
            const range = rangeEl.value;

            fetch(
                    `{{ route('admin.produce.analytics.data') }}?product=${product}&municipality=${municipality}&range=${range}`)
                .then(res => res.json())
                .then(data => {
                    renderTrend(data.trends);
                    renderProductDistribution(data.productDistribution);
                    renderAvgPrice(data.avgPrice);
                    renderYield(data.yieldPerFarmer);
                });
        }

        function renderTrend(data) {
            charts.trend?.destroy();

            charts.trend = new Chart(trendChart, {
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

        function renderProductDistribution(data) {
            charts.product?.destroy();

            charts.product = new Chart(productChart, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.product),
                    datasets: [{
                        label: 'Total Quantity',
                        data: data.map(d => d.total),
                    }]
                }
            });
        }

        function renderAvgPrice(data) {
            charts.avg?.destroy();

            charts.avg = new Chart(avgPriceChart, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.municipality),
                    datasets: [{
                        label: 'Average Price',
                        data: data.map(d => d.avg_price),
                    }]
                }
            });
        }

        function renderYield(data) {
            charts.yield?.destroy();

            charts.yield = new Chart(yieldChart, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.name),
                    datasets: [{
                        label: 'Total Yield',
                        data: data.map(d => d.total_quantity),
                    }]
                }
            });
        }

        productEl.addEventListener('change', loadAnalytics);
        municipalityEl.addEventListener('change', loadAnalytics);
        rangeEl.addEventListener('change', loadAnalytics);

        loadAnalytics();
    </script>

</x-layouts.adapp>
