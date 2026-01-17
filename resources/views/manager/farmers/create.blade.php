<x-layouts.mapp title="Add Farmer">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- FORM --}}
        <form
            action="{{ route('manager.farmers.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4 bg-white dark:bg-zinc-900 p-6 rounded-xl border"
        >
            @csrf

            {{-- Name --}}
            <div>
                <label class="text-sm font-medium">Name</label>
                <input
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full border rounded-lg p-2"
                    required
                >
            </div>

            {{-- Contact --}}
            <div>
                <label class="text-sm font-medium">Contact</label>
                <input
                    name="contact"
                    value="{{ old('contact') }}"
                    class="w-full border rounded-lg p-2"
                >
            </div>

            {{-- Municipality (readonly) --}}
            <div>
                <label class="text-sm font-medium">Municipality</label>
                <input
                    type="text"
                    name="municipality"
                    value="{{ auth()->user()->municipality }}"
                    class="w-full border rounded-lg p-2 bg-zinc-100 dark:bg-zinc-700"
                    readonly
                >
            </div>

            {{-- Barangay --}}
            <div>
                <label class="text-sm font-medium">Barangay</label>
                <select
                    id="barangay"
                    name="barangay"
                    class="w-full border rounded-lg p-2"
                    required
                >
                    <option value="">Select barangay</option>
                </select>
            </div>

            {{-- Coordinates --}}
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-sm font-medium">Latitude</label>
                    <input
                        id="latitude"
                        name="latitude"
                        class="w-full border rounded-lg p-2"
                        readonly
                    >
                </div>
                <div>
                    <label class="text-sm font-medium">Longitude</label>
                    <input
                        id="longitude"
                        name="longitude"
                        class="w-full border rounded-lg p-2"
                        readonly
                    >
                </div>
            </div>

            {{-- Farmer Image --}}
            <div>
                <label class="text-sm font-medium">Farmer Image</label>
                <input
                    type="file"
                    name="image"
                    accept="image/*"
                    class="w-full text-sm"
                >
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 pt-4">
                <a
                    href="{{ route('manager.farmers.index') }}"
                    class="px-4 py-2 rounded-lg border"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="bg-primary text-white px-4 py-2 rounded-lg"
                >
                    Save Farmer
                </button>
            </div>
        </form>

        {{-- MAP --}}
        <div
            id="map"
            class="w-full h-[500px] rounded-xl border"
            wire:ignore
        ></div>
    </div>

    {{-- SCRIPT --}}
    <script>
        let map, marker, geojsonData;
        const municipality = "{{ auth()->user()->municipality }}"; // fixed user municipality
        const barangaySelect = document.getElementById('barangay');

        function initMap() {
            // Default center (will update based on municipality)
            let defaultLatLng = [18.1647, 120.7116];

            map = L.map('map').setView(defaultLatLng, 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker(defaultLatLng, { draggable: true }).addTo(map);

            fetch('/maps/ilocos-norte.geojson')
                .then(r => r.json())
                .then(data => {
                    geojsonData = data;

                    // Filter features to user's municipality
                    const userFeatures = geojsonData.features.filter(f => f.properties.Municipality === municipality);

                    if (userFeatures.length) {
                        // Set map center to first barangay in the municipality
                        const firstCoords = userFeatures[0].geometry.coordinates[0];
                        const center = getPolygonCenter(firstCoords);
                        map.setView(center, 12);
                        marker.setLatLng(center);
                        updateLatLng(center);
                    }

                    populateBarangays(data);

                    L.geoJSON(userFeatures, {
                        style: { color: '#2563eb', weight: 1, fillOpacity: 0.2 }
                    }).addTo(map);
                });

            marker.on('dragend', onMarkerMoved);
        }

        function populateBarangays(data) {
            const barangays = data.features
                .filter(f => f.properties.Municipality === municipality)
                .map(f => f.properties.Barangay);

            [...new Set(barangays)].forEach(b => {
                barangaySelect.innerHTML += `<option value="${b}">${b}</option>`;
            });
        }

        barangaySelect.addEventListener('change', () => {
            const feature = geojsonData.features.find(
                f => f.properties.Municipality === municipality &&
                     f.properties.Barangay === barangaySelect.value
            );

            if (!feature) return;

            const center = getPolygonCenter(feature.geometry.coordinates[0]);
            marker.setLatLng(center);
            map.setView(center, 14);
            updateLatLng(center);
        });

        function onMarkerMoved(e) {
            const latlng = e.target.getLatLng();
            updateLatLng(latlng);
        }

        function updateLatLng(latlng) {
            document.getElementById('latitude').value = latlng[0] ? latlng[0].toFixed(7) : latlng.lat.toFixed(7);
            document.getElementById('longitude').value = latlng[1] ? latlng[1].toFixed(7) : latlng.lng.toFixed(7);
        }

        function getPolygonCenter(coords) {
            let lat = 0, lng = 0;
            coords.forEach(c => {
                lng += c[0];
                lat += c[1];
            });
            return [lat / coords.length, lng / coords.length];
        }

        document.addEventListener('DOMContentLoaded', initMap);
        document.addEventListener('livewire:navigated', initMap);
    </script>
</x-layouts.mapp>
