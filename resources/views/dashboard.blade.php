<x-layouts.app :title="__('Dashboard')">

    <div class="p-6 space-y-8">

        {{-- Hero / Introduction --}}
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-gradient-to-br from-white to-neutral-100 dark:from-zinc-800 dark:to-zinc-900 p-8 shadow-sm">
            
            <div class="max-w-3xl space-y-3">
                <h1 class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">
                    Ilocos Norte Agricultural Platform
                </h1>

                <p class="text-neutral-600 dark:text-neutral-300 leading-relaxed">
                    Connecting <span class="font-medium text-green-600 dark:text-green-400">local farmers</span> directly with consumers. 
                    Explore municipalities, discover available agricultural products, and support a more transparent and efficient local food system.
                </p>
            </div>

            {{-- Decorative Accent --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-green-500/10 rounded-full blur-2xl"></div>
        </div>


        {{-- Map Section --}}
        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                        Map Overview
                    </h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                       GIS Based Marketing Platform for Ilocos Norte's Agricultural Products
                    </p>
                </div>

                {{-- Optional Badge --}}
                <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">
                    Live Data
                </span>
            </div>

            {{-- Map Container --}}
            <div class="p-4">
                <div
                    wire:ignore
                    id="map"
                    class="w-full h-[520px] rounded-xl border border-neutral-300 dark:border-zinc-700 shadow-inner"
                ></div>
            </div>
        </div>

    </div>

    <script>
        function initIlocosMap() {
            const el = document.getElementById('map');
            if (!el || el.dataset.loaded) return;
            el.dataset.loaded = true;

            const map = L.map(el, {
                zoomControl: false
            }).setView([18.2500, 120.5000], 10);

            // Custom zoom position
            L.control.zoom({ position: 'bottomright' }).addTo(map);

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

                    const specialColors = {
                        'Laoag': '#f59e0b',
                        'Vintar': '#10b981'
                    };

                    L.geoJSON(data, {
                        style: feature => {
                            const name = feature.properties.Municipality;

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
                                className: 'text-xs font-semibold px-2 py-1 bg-white dark:bg-zinc-800 rounded shadow'
                            });

                            // Hover effect
                            layer.on({
                                mouseover: e => {
                                    e.target.setStyle({
                                        weight: 3,
                                        fillOpacity: 0.5
                                    });
                                },
                                mouseout: e => {
                                    e.target.setStyle({
                                        weight: 2,
                                        fillOpacity: 0.35
                                    });
                                }
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