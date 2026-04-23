<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Name -->
            <flux:input name="name" :label="__('Name')" :value="old('name')" type="text" required autofocus />

            <!-- Email -->
            <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required />
            
            <!-- Contact Number -->
            <flux:input name="contact_number" :label="__('Contact Number')" :value="old('contact_number')" type="text" required />


            <!-- Municipality -->
            <div>
                <label class="block text-sm font-medium mb-1">Municipality</label>
                <select id="municipality" name="municipality"
                    class="w-full border rounded-lg p-2" required>
                    <option value="">Select Municipality</option>
                </select>
            </div>

            <!-- Barangay -->
            <div>
                <label class="block text-sm font-medium mb-1">Barangay</label>
                <select id="barangay" name="barangay"
                    class="w-full border rounded-lg p-2" required>
                    <option value="">Select Barangay</option>
                </select>
            </div>

            <!-- Password -->
            <flux:input name="password" :label="__('Password')" type="password" required />

            <!-- Confirm Password -->
            <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required />

            <flux:button type="submit" variant="primary" class="w-full">
                Create account
            </flux:button>
        </form>
        

        <div class="text-center text-sm text-zinc-600">
            Already have an account?
            <flux:link :href="route('login')">Log in</flux:link>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
        let geojsonData;
        const municipalitySelect = document.getElementById('municipality');
        const barangaySelect = document.getElementById('barangay');

        async function initLocationDropdowns() {

            try {
                const res = await fetch('/maps/ilocos-norte.geojson');
                geojsonData = await res.json();

                // Extract unique municipalities
                const municipalities = [
                    ...new Set(
                        geojsonData.features.map(f => f.properties.Municipality)
                    )
                ];

                // Populate municipality dropdown
                municipalities.forEach(muni => {
                    municipalitySelect.innerHTML += `<option value="${muni}">${muni}</option>`;
                });

            } catch (error) {
                console.error("GeoJSON load error:", error);
            }
        }

        // When municipality changes → filter barangays
        municipalitySelect.addEventListener('change', () => {

            const selectedMuni = municipalitySelect.value;

            // Reset barangay dropdown
            barangaySelect.innerHTML = `<option value="">Select Barangay</option>`;

            const filteredBarangays = geojsonData.features
                .filter(f => f.properties.Municipality === selectedMuni)
                .map(f => f.properties.Barangay);

            [...new Set(filteredBarangays)].forEach(brgy => {
                barangaySelect.innerHTML += `<option value="${brgy}">${brgy}</option>`;
            });
        });

        document.addEventListener('DOMContentLoaded', initLocationDropdowns);
    </script>
</x-layouts.auth>
