<x-layouts.adapp title="Products">
    <div class="space-y-6 p-6 max-w-6xl">

        <!-- Header Section -->
        <div class="flex justify-between items-start">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
                    Product Categories
                </h1>
                <p class="text-zinc-600 dark:text-zinc-400">
                    Manage farm produce categories available in the marketplace
                </p>
            </div>

            <a
                href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all shadow-md hover:shadow-lg"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Category
            </a>
        </div>

        <!-- Products Table -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-primary-200 dark:border-primary-900 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30 border-b border-primary-200 dark:border-primary-900">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Product Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Created</th>
                            <th class="px-6 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100 dark:divide-primary-900">
                        @forelse($products as $product)
                            <tr class="hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $product->product_name }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $product->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.products.edit', $product) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-primary-100 hover:bg-primary-200 text-primary-700 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 dark:text-primary-300 rounded-lg transition-colors font-medium"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.products.destroy', $product) }}"
                                            onsubmit="return confirm('Delete this product category? This action cannot be undone.')"
                                            class="inline"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-2 px-4 py-2 text-sm text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-300 rounded-lg transition-colors font-medium"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 px-6 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-16 h-16 text-primary-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-zinc-600 dark:text-zinc-400 font-medium">No product categories found</p>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-500">Get started by creating your first product category</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.adapp>