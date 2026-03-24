<x-layouts.app title="Preorder">

    <div class="max-w-xl mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-semibold text-center">Preorder Product</h1>

        {{-- Scrollable & Zoomable Image --}}
        <div class="overflow-hidden rounded shadow-lg border">
            @if ($produce->image)
                <div class="relative w-full h-60 overflow-hidden cursor-zoom-in">
                    <img src="{{ Storage::url($produce->image) }}"
                         alt="{{ $produce->product }}"
                         id="zoomable-image"
                         class="w-full h-full object-cover transition-transform duration-200 ease-in-out"
                         style="touch-action: pinch-zoom; transform: scale(1);">
                </div>
            @else
                <div class="w-full h-60 flex items-center justify-center bg-zinc-200 dark:bg-zinc-700 rounded">
                    <span class="text-zinc-500">No Image</span>
                </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div class="border rounded p-4 space-y-2">
            <p><strong>Product:</strong> {{ $produce->product }}</p>
            <p><strong>About Product:</strong> {{ $produce->description }}</p>
            <p><strong>Farmer:</strong> {{ $produce->farmer->name }}</p>
            <p><strong>Contact:</strong> {{ $produce->farmer->contact }}</p>
            <p><strong>Price:</strong> ₱{{ $produce->price }}</p>
            <p><strong>Available:</strong> {{ $produce->availableQuantity() }}</p>
        </div>

        {{-- Preorder Form --}}
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

    {{-- Zoom/Scroll Script --}}
    <script>
        const img = document.getElementById('zoomable-image');
        let scale = 1;

        img.addEventListener('wheel', (e) => {
            e.preventDefault();
            if (e.deltaY < 0) {
                scale += 0.1; // zoom in
            } else {
                scale -= 0.1; // zoom out
                if(scale < 1) scale = 1; // don't shrink below original
            }
            img.style.transform = `scale(${scale})`;
        });
    </script>

</x-layouts.app>
