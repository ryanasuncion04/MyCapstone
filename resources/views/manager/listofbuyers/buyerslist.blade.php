<x-layouts.mapp :title="__('List of Buyers')">

    <div class="p-6 space-y-4">

        <h1 class="text-2xl font-semibold">List of Buyers</h1>

        <!-- FILTERS -->
        <form method="GET" class="flex gap-3 mb-4">

            <!-- SEARCH -->
            <input 
                type="text" 
                name="search"
                placeholder="Search by name"
                value="{{ request('search') }}"
                class="border rounded px-3 py-2 w-1/3"
            >

            <!-- MUNICIPALITY -->
            <select name="municipality" class="border rounded px-3 py-2">
                <option value="">All Municipality</option>

                @foreach ($municipalities as $mun)
                    <option value="{{ $mun }}" 
                        {{ request('municipality') == $mun ? 'selected' : '' }}>
                        {{ $mun }}
                    </option>
                @endforeach
            </select>

            <button class="bg-green-600 text-white px-4 py-2 rounded">
                Search
            </button>

        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg">

                <!-- HEADER -->
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Municipality</th>
                        <th class="px-4 py-2 text-left">Barangay</th>
                        <th class="px-4 py-2 text-left">Contact Information</th>
                    </tr>
                </thead>

                <!-- BODY -->
                <tbody>
                    @forelse ($buyers as $buyer)
                        <tr class="border-t hover:bg-gray-100">

                            <td class="px-4 py-2">{{ $buyer->name }}</td>
                            <td class="px-4 py-2">{{ $buyer->email }}</td>
                            <td class="px-4 py-2">{{ $buyer->municipality }}</td>
                            <td class="px-4 py-2">{{ $buyer->barangay }}</td>
                            <td class="px-4 py-2">{{ $buyer->contact_number }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">
                                No buyers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

    <!-- CHAT SCRIPT -->
    <script>
        function startChat(userId) {
            fetch("{{ route('chat.start') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        user_id: userId
                    })
                })
                .then(res => {
                    if (res.redirected) {
                        window.location.href = res.url;
                    }
                });
        }
    </script>

</x-layouts.mapp>
