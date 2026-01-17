<x-layouts.app :title="__('Dashboard')">

    <div class="p-6 space-y-4">
        <h1 class="text-2xl font-semibold">Ilocos Norte Map</h1>

        <div
            wire:ignore
            id="map"
            class="w-full h-[500px] rounded-xl border border-zinc-300 dark:border-zinc-700"
        ></div>
    </div>

    <script>
        function initIlocosMap() {
            const el = document.getElementById('map');
            if (!el || el.dataset.loaded) return;
            el.dataset.loaded = true;

            const map = L.map(el).setView([18.1647, 120.7116], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            /**
             * Color palette for municipalities
             */
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
                    L.geoJSON(data, {
                        style: feature => {
                            const name = feature.properties.Municipality;

                            if (!municipalityColors[name]) {
                                municipalityColors[name] =
                                    colors[colorIndex % colors.length];
                                colorIndex++;
                            }

                            return {
                                color: municipalityColors[name],
                                weight: 2,
                                fillOpacity: 0.35
                            };
                        },
                        onEachFeature: (feature, layer) => {
                            layer.bindTooltip(
                                feature.properties.Municipality,
                                {
                                    sticky: true,
                                    direction: 'center',
                                    className: 'font-semibold text-sm'
                                }
                            );
                        }
                    }).addTo(map);

                    setTimeout(() => map.invalidateSize(), 300);
                });
        }

        document.addEventListener('DOMContentLoaded', initIlocosMap);
        document.addEventListener('livewire:navigated', initIlocosMap);
    </script>

</x-layouts.app>
