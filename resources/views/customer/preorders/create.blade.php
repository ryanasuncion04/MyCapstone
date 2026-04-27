<x-layouts.app title="Preorder">

    <div class="max-w-3xl mx-auto p-6 space-y-6">

        <!-- Header -->
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
                Place Your Order
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400">
                Order fresh produce directly from the farmer
            </p>
        </div>

        <!-- Product Image -->
        <div class="rounded-xl overflow-hidden shadow-md border border-primary-200 dark:border-primary-900">
            @if ($produce->image)
                <div class="relative w-full h-72 overflow-hidden bg-gradient-to-br from-primary-100 to-cream-100">
                    <img
                        src="{{ Storage::url($produce->image) }}"
                        alt="{{ $produce->product }}"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                    >
                </div>
            @else
                <div class="w-full h-72 flex items-center justify-center bg-gradient-to-br from-primary-100 to-cream-100">
                    <svg class="w-24 h-24 text-primary-200" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Product + Order -->
        <div class="grid md:grid-cols-3 gap-6">

            <!-- Left Column -->
            <div class="md:col-span-2 space-y-4">

                <!-- Farmer Card -->
                <div class="bg-green-600 rounded-xl p-5 text-white shadow-md">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h2 class="text-xl font-bold mb-1">
                                {{ $produce->farmer->name }}
                            </h2>

                            <p class="text-green-100 flex items-center gap-2 mb-2">
                                📍 {{ $produce->farmer->barangay }},
                                {{ $produce->farmer->municipality }}
                            </p>

                            <p class="text-green-100">
                                ☎️
                                <a
                                    href="tel:{{ $produce->farmer->contact }}"
                                    class="hover:underline"
                                >
                                    {{ $produce->farmer->contact }}
                                </a>
                            </p>
                        </div>

                        <button
                            type="button"
                            onclick="startChat({{ $produce->user_id }})"
                            class="bg-white text-green-700 hover:bg-green-50 px-4 py-2 rounded-lg font-semibold shadow-sm whitespace-nowrap"
                        >
                            💬 Message Farmer
                        </button>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-primary-200 dark:border-primary-900 p-5 space-y-3">

                    <div>
                        <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mb-1">
                            {{ $produce->product }}
                        </h3>

                        @if ($produce->description)
                            <p class="text-zinc-600 dark:text-zinc-400">
                                {{ $produce->description }}
                            </p>
                        @endif
                    </div>

                    <div class="border-t border-primary-200 dark:border-primary-900 pt-3 space-y-2">

                        <div class="flex justify-between items-center">
                            <span class="text-zinc-600 dark:text-zinc-400">
                                Availability Period:
                            </span>

                            <span class="font-medium text-zinc-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($produce->available_from)->format('M d') }}
                                -
                                {{ \Carbon\Carbon::parse($produce->available_until)->format('M d, Y') }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-zinc-600 dark:text-zinc-400">
                                Stock Available:
                            </span>

                            <span class="font-bold text-green-600 text-lg">
                                {{ $produce->availableQuantity() }} kg
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div>
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-primary-200 dark:border-primary-900 p-5 sticky top-6 space-y-4">

                    <!-- Price -->
                    <div class="border-b border-primary-200 dark:border-primary-900 pb-4">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">
                            Price per kilogram
                        </p>

                        <p class="text-3xl font-bold text-green-700">
                            ₱{{ number_format($produce->price, 2) }}
                        </p>
                    </div>

                    <!-- Order Form -->
                    <form
                        id="preorderForm"
                        method="POST"
                        action="{{ route('customer.preorders.store', $produce) }}"
                        class="space-y-4"
                    >
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Order Quantity (kg)
                            </label>

                            <input
                                type="number"
                                name="quantity"
                                min="1"
                                max="{{ $produce->availableQuantity() }}"
                                value="1"
                                required
                                class="w-full px-4 py-2.5 rounded-lg border border-primary-300 dark:border-primary-700 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                            >

                            <p class="text-xs text-zinc-500 mt-1">
                                Max: {{ $produce->availableQuantity() }} kg available
                            </p>
                        </div>

                        <!-- Total -->
                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">
                                Total Price
                            </p>

                            <p class="text-2xl font-bold text-green-700">
                                ₱<span id="totalPrice">
                                    {{ number_format($produce->price, 2) }}
                                </span>
                            </p>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold shadow-md"
                        >
                            Place Order
                        </button>

                    </form>

                    <p class="text-xs text-zinc-500 text-center">
                        ✓ Secure transaction • Direct from farmer
                    </p>

                </div>
            </div>

        </div>
    </div>

    <script>
        const pricePerKg = {{ $produce->price }};
        const quantityInput = document.querySelector('input[name="quantity"]');
        const totalPriceSpan = document.getElementById('totalPrice');

        if (quantityInput) {
            quantityInput.addEventListener('input', function () {

                const quantity = parseFloat(this.value) || 1;

                const total = (pricePerKg * quantity).toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                totalPriceSpan.textContent = total;
            });
        }

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
