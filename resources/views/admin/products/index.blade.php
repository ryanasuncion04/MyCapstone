<x-layouts.adapp title="Products">
    <div class="max-w-4xl bg-white dark:bg-zinc-900 p-6 rounded-xl border">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Products</h1>

            <a
                href="{{ route('admin.products.create') }}"
                class="bg-primary text-white px-4 py-2 rounded-lg"
            >
                Add Product
            </a>
        </div>

        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Product Name</th>
                    <th class="text-left py-2">Created</th>
                    <th class="text-right py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-b">
                        <td class="py-2">{{ $product->product_name }}</td>
                        <td class="py-2 text-sm text-zinc-500">
                            {{ $product->created_at->format('M d, Y') }}
                        </td>
                        <td class="py-2 text-right">
                            <div class="flex justify-end gap-2">
                                <a
                                    href="{{ route('admin.products.edit', $product) }}"
                                    class="px-3 py-1 text-sm border rounded-lg"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.products.destroy', $product) }}"
                                    onsubmit="return confirm('Delete this product?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="px-3 py-1 text-sm text-red-600 border border-red-300 rounded-lg"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-zinc-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.adapp>