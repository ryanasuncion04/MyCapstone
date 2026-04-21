<x-layouts.app title="Preorder">

    <div class="max-w-xl mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-semibold text-center">Preorder Product</h1>

        {{-- Image --}}
        <div class="overflow-hidden rounded shadow-lg border">
            @if ($produce->image)
                <div class="relative w-full h-60 overflow-hidden">
                    <img src="{{ Storage::url($produce->image) }}" alt="{{ $produce->product }}"
                        class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-full h-60 flex items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                    <span class="text-zinc-500">No Image</span>
                </div>
            @endif
        </div>





        {{-- Product Info --}}
        <div class="border rounded p-4 space-y-3">
            {{-- FARMER SECTION --}}
            <div class="flex items-center justify-between border-b pb-3">

                {{-- Farmer Info (LEFT) --}}
                <div>
                    <p class="font-medium">{{ $produce->farmer->name }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $produce->farmer->contact }}
                    </p>
    
                    <p class="text-s text-gray-500 flex items-center gap-1">
                        {{ $produce->farmer->barangay }},
                        {{ $produce->farmer->municipality }}
                    </p>

                </div>

                {{-- Message Button (RIGHT aligned) --}}
                <button type="button" onclick="startChat({{ $produce->user_id }})"
                    class="bg-gray-800 text-white text-xs px-3 py-2 rounded hover:bg-gray-900 whitespace-nowrap">
                    💬 Message
                </button>

            </div>
            <p><strong>Product:</strong> {{ $produce->product }}</p>

            <p><strong>Description:</strong> {{ $produce->description ?? '—' }}</p>

            <p>
                <strong>Date Available:</strong><br>
                <span class="text-sm text-gray-700">
                    {{ \Carbon\Carbon::parse($produce->available_from)->format('F d, Y') }}
                    →
                    {{ \Carbon\Carbon::parse($produce->available_until)->format('F d, Y') }}
                </span>
            </p>
            <p><strong>Price:</strong> ₱{{ number_format($produce->price, 2) }}</p>

            <p><strong>Available Stock:</strong> {{ $produce->availableQuantity() }}</p>

            {{-- PREORDER FORM --}}
            <form id="preorderForm" method="POST" action="{{ route('customer.preorders.store', $produce) }}"
                class="space-y-3 mt-3">
                @csrf

                <div>
                    <label class="block text-sm font-medium">Quantity</label>
                    <input type="number" name="quantity" min="1" max="{{ $produce->availableQuantity() }}"
                        required class="w-full border rounded px-3 py-2">
                </div>
            </form>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-2">

            {{-- PREORDER --}}
            <button type="submit" form="preorderForm"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Place Preorder
            </button>
        </div>
    </div>

    {{-- CHAT FUNCTION --}}
    <script>
        function startChat(managerId) {
            fetch("{{ route('chat.start') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        user_id: managerId
                    })
                })
                .then(res => {
                    if (res.redirected) {
                        window.location.href = res.url;
                    }
                });
        }
    </script>

</x-layouts.app>
