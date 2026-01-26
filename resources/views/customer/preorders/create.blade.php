<x-layouts.app title="Preorder">

    <div class="max-w-xl mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-semibold">Preorder Product</h1>

        <div class="border rounded p-4 space-y-2">
            <p><strong>Product:</strong> {{ $produce->product }}</p>
            <p><strong>Farmer:</strong> {{ $produce->farmer->name }}</p>
            <p><strong>Price:</strong> ₱{{ $produce->price }}</p>
            <p><strong>Available:</strong> {{ $produce->availableQuantity() }}</p>
        </div>

        <form method="POST" action="{{ route('customer.preorders.store', $produce) }}">
            @csrf

            <div>
                <label class="block text-sm font-medium">Quantity</label>
                <input type="number" name="quantity" min="1" max="{{ $produce->availableQuantity() }}" required
                    class="w-full border rounded px-3 py-2">
            </div>

            <button type="submit" class="mt-4 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Place Preorder
            </button>
        </form>
    </div>

</x-layouts.app>
