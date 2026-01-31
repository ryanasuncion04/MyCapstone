<x-layouts.adapp title="Produce Analytics">

    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-bold">Farm Produce Analytics</h1>

        {{-- Filters --}}
        <div class="flex gap-4">
            <select id="product" class="border rounded p-2">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                @endforeach
            </select>

            <select id="range" class="border rounded p-2">
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
                <option value="daily">Daily</option>
            </select>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <canvas id="trendChart">Produce Trend</canvas>
            <canvas id="comparisonChart">Monthly Comparison</canvas>
            <canvas id="avgPriceChart">Average Price</canvas>
            <canvas id="yieldChart">Yield per Farmer</canvas>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let charts = {};

        function loadAnalytics() {
            const product = document.getElementById('product').value;
            const range = document.getElementById('range').value;

            fetch(`{{ route('admin.produce.analytics.data') }}?product=${product}&range=${range}`)
                .then(res => res.json())
                .then(data => {
                    renderTrendChart(data.trends);
                    renderComparisonChart(data.comparison);
                    renderAvgPriceChart(data.avgPrice);
                    renderYieldChart(data.yieldPerFarmer);
                });
        }

        function renderTrendChart(data) {
            const ctx = document.getElementById('trendChart');

            charts.trend?.destroy();

            charts.trend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.period),
                    datasets: [{
                        label: 'Total Produce',
                        data: data.map(d => d.total_quantity),
                        borderWidth: 2
                    }]
                }
            });
        }

        function renderComparisonChart(data) {
            const ctx = document.getElementById('comparisonChart');

            charts.comparison?.destroy();

            charts.comparison = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => `${d.year}-${d.month}`),
                    datasets: [{
                        label: 'Monthly Comparison',
                        data: data.map(d => d.total_quantity)
                    }]
                }
            });
        }

        function renderAvgPriceChart(data) {
            const ctx = document.getElementById('avgPriceChart');

            charts.avg?.destroy();

            charts.avg = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => `${d.product} (${d.municipality})`),
                    datasets: [{
                        label: 'Avg Price',
                        data: data.map(d => d.avg_price)
                    }]
                }
            });
        }

        function renderYieldChart(data) {
            const ctx = document.getElementById('yieldChart');

            charts.yield?.destroy();

            charts.yield = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.name),
                    datasets: [{
                        label: 'Total Produce',
                        data: data.map(d => d.total_quantity)
                    }]
                }
            });
        }

        document.getElementById('product').addEventListener('change', loadAnalytics);
        document.getElementById('range').addEventListener('change', loadAnalytics);

        loadAnalytics();
    </script>

</x-layouts.adapp>
