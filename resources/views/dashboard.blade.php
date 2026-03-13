<x-layouts.app :title="__('Dashboard')">

    <div class="p-6 space-y-6">

        {{-- System Introduction --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
            <h1 class="text-2xl font-semibold mb-2">Welcome to the Ilocos Norte Agricultural Platform</h1>
            <p class="text-neutral-700 dark:text-neutral-300 text-sm">
                This platform connects smallholder farmers in Ilocos Norte directly with consumers. 
                You can view the locations of participating farmers and the availability of agricultural products
                in different municipalities. Our goal is to make the local agricultural market more transparent,
                efficient, and beneficial for both farmers and buyers.
            </p>
        </div>

        {{-- Ilocos Norte Map --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Ilocos Norte Map Overview</h2>
            <div
                wire:ignore
                id="map"
                class="w-full h-[500px] rounded-xl border border-zinc-300 dark:border-zinc-700"
            ></div>
        </div>

    </div>

    <script>
        function initIlocosMap() {
            const el = document.getElementById('map');
            if (!el || el.dataset.loaded) return;
            el.dataset.loaded = true;

            // Set initial view centered on Ilocos Norte
            const map = L.map(el).setView([18.2500, 120.5000], 10); // adjust zoom for better focus

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const colors = [
                '#2563eb', '#16a34a', '#dc2626', '#7c3aed',
                '#ea580c', '#0891b2', '#9333ea', '#65a30d',
                '#be123c', '#0f766e'
            ];

            const municipalityColors = {};
            let colorIndex = 0;

            fetch('/maps/ilocos-norte.geojson')
                .then(res => res.json())
                .then(data => {

                    // Assign custom colors to Laoag and Vintar first
                    const specialColors = {
                        'Laoag': '#ff9900',  // orange
                        'Vintar': '#00cc99'  // teal
                    };

                    L.geoJSON(data, {
                        style: feature => {
                            const name = feature.properties.Municipality;

                            // Use special color if municipality is Laoag or Vintar
                            if (specialColors[name]) {
                                municipalityColors[name] = specialColors[name];
                            } else if (!municipalityColors[name]) {
                                municipalityColors[name] = colors[colorIndex % colors.length];
                                colorIndex++;
                            }

                            return {
                                color: municipalityColors[name],
                                weight: 2,
                                fillOpacity: 0.35
                            };
                        },
                        onEachFeature: (feature, layer) => {
                            layer.bindTooltip(feature.properties.Municipality, {
                                sticky: true,
                                direction: 'center',
                                className: 'font-semibold text-sm'
                            });
                        }
                    }).addTo(map);

                    setTimeout(() => map.invalidateSize(), 300);
                });
        }

        document.addEventListener('DOMContentLoaded', initIlocosMap);
        document.addEventListener('livewire:navigated', initIlocosMap);
    </script>

</x-layouts.app>
