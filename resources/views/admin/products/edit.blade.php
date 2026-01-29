<x-layouts.adapp title="Edit Product">
    <div class="max-w-md bg-white dark:bg-zinc-900 p-6 rounded-xl border">
        <h1 class="text-xl font-semibold mb-4">Edit Product</h1>

        <form
            method="POST"
            action="{{ route('admin.products.update', $product) }}"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Product Name</label>
                <input
                    type="text"
                    name="product_name"
                    value="{{ old('product_name', $product->product_name) }}"
                    class="w-full border rounded-lg p-2"
                    required
                >
                @error('product_name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a
                    href="{{ route('admin.products.index') }}"
                    class="px-4 py-2 border rounded-lg"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="bg-primary text-white px-4 py-2 rounded-lg"
                >
                    Update Product
                </button>
            </div>
        </form>
    </div>
</x-layouts.adapp>