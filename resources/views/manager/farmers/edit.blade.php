<x-layouts.mapp title="Edit Farmer">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- FORM --}}
        <form
            action="{{ route('manager.farmers.update', $farmer) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4 bg-white dark:bg-zinc-900 p-6 rounded-xl border"
        >
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label class="text-sm font-medium">Name</label>
                <input
                    name="name"
                    value="{{ old('name', $farmer->name) }}"
                    class="w-full border rounded-lg p-2"
                    required
                >
            </div>

            {{-- Contact --}}
            <div>
                <label class="text-sm font-medium">Contact</label>
                <input
                    name="contact"
                    value="{{ old('contact', $farmer->contact) }}"
                    class="w-full border rounded-lg p-2"
                >
            </div>

            {{-- Municipality --}}
            <div>
                <label class="text-sm font-medium">Municipality</label>
                <select
                    id="municipality"
                    name="municipality"
                    class="w-full border rounded-lg p-2"
                    required
                >
                    <option value="">Select municipality</option>
                </select>
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
                        value="{{ old('latitude', $farmer->latitude) }}"
                        class="w-full border rounded-lg p-2"
                        readonly
                    >
                </div>
                <div>
                    <label class="text-sm font-medium">Longitude</label>
                    <input
                        id="longitude"
                        name="longitude"
                        value="{{ old('longitude', $farmer->longitude) }}"
                        class="w-full border rounded-lg p-2"
                        readonly
                    >
                </div>
            </div>

            {{-- Current Image --}}
            @if ($farmer->image)
                <div>
                    <label class="text-sm font-medium">Current Image</label>
                    <img
                        src="{{ Storage::url($farmer->image) }}"
                        class="h-24 w-24 rounded object-cover mt-2"
                    >
                </div>
            @endif

            {{-- Replace Image --}}
            <div>
                <label class="text-sm font-medium">Replace Image</label>
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
                    Update Farmer
                </button>
            </div>
        </form>

        {{-- MAP --}}
        <div id="map" class="w-full h-[500px] rounded-xl border" wire:ignore></div>
    </div>

    {{-- SCRIPT --}}
    <script>
        let map, marker, geojsonData;

        const municipalitySelect = document.getElementById('municipality');
        const barangaySelect = document.getElementById('barangay');

        const initial = {
            lat: {{ $farmer->latitude }},
            lng: {{ $farmer->longitude }},
            municipality: @json($farmer->municipality),
            barangay: @json($farmer->barangay)
        };

        function initMap() {
            map = L.map('map').setView([initial.lat, initial.lng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([initial.lat, initial.lng], {
                draggable: true
            }).addTo(map);

            fetch('/maps/ilocos-norte.geojson')
                .then(r => r.json())
                .then(data => {
                    geojsonData = data;

                    populateMunicipalities();
                    preloadSelections();

                    L.geoJSON(data, {
                        style: { color: '#2563eb', weight: 1, fillOpacity: 0.2 }
                    }).addTo(map);
                });

            marker.on('dragend', onMarkerMoved);
        }

        function populateMunicipalities() {
            const municipalities = [...new Set(
                geojsonData.features.map(f => f.properties.Municipality)
            )];

            municipalities.forEach(m => {
                municipalitySelect.innerHTML += `<option value="${m}">${m}</option>`;
            });
        }

        function preloadSelections() {
            municipalitySelect.value = initial.municipality;
            updateBarangays();

            setTimeout(() => {
                barangaySelect.value = initial.barangay;
            }, 50);
        }

        function updateBarangays() {
            barangaySelect.innerHTML = '<option value="">Select barangay</option>';

            geojsonData.features
                .filter(f => f.properties.Municipality === municipalitySelect.value)
                .forEach(b => {
                    barangaySelect.innerHTML += `
                        <option value="${b.properties.Barangay}">
                            ${b.properties.Barangay}
                        </option>`;
                });
        }

        municipalitySelect.addEventListener('change', () => {
            updateBarangays();
        });

        barangaySelect.addEventListener('change', () => {
            const feature = geojsonData.features.find(
                f =>
                    f.properties.Municipality === municipalitySelect.value &&
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

            geojsonData.features.forEach(f => {
                if (pointInPolygon(latlng, f.geometry.coordinates[0])) {
                    municipalitySelect.value = f.properties.Municipality;
                    updateBarangays();
                    barangaySelect.value = f.properties.Barangay;
                }
            });
        }

        function updateLatLng(latlng) {
            document.getElementById('latitude').value = latlng.lat.toFixed(7);
            document.getElementById('longitude').value = latlng.lng.toFixed(7);
        }

        function getPolygonCenter(coords) {
            let lat = 0, lng = 0;
            coords.forEach(c => {
                lng += c[0];
                lat += c[1];
            });
            return {
                lat: lat / coords.length,
                lng: lng / coords.length
            };
        }

        function pointInPolygon(point, vs) {
            let x = point.lng, y = point.lat;
            let inside = false;

            for (let i = 0, j = vs.length - 1; i < vs.length; j = i++) {
                let xi = vs[i][0], yi = vs[i][1];
                let xj = vs[j][0], yj = vs[j][1];

                let intersect =
                    ((yi > y) !== (yj > y)) &&
                    (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                if (intersect) inside = !inside;
            }
            return inside;
        }

        document.addEventListener('DOMContentLoaded', initMap);
        document.addEventListener('livewire:navigated', initMap);
    </script>
</x-layouts.mapp>
