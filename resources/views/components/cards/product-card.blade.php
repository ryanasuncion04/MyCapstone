@props(['product', 'farmer' => null])

<div class="group rounded-xl overflow-hidden bg-white dark:bg-zinc-800 shadow-sm hover:shadow-md transition-all duration-300 border border-primary-100 dark:border-primary-900 hover:border-primary-300 dark:hover:border-primary-700">
    
    <!-- Image Container -->
    <div class="relative w-full h-48 bg-gradient-to-br from-primary-100 to-cream-100 overflow-hidden">
        @if ($product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-16 h-16 text-primary-200" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                </svg>
            </div>
        @endif
        
        <!-- Status Badge -->
        <div class="absolute top-3 right-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-100">
                {{ $product->quantity }} in stock
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4 space-y-3">
        
        <!-- Product Name -->
        <h3 class="font-semibold text-base text-zinc-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
            {{ $product->name }}
        </h3>

        <!-- Price -->
        <div class="flex items-baseline gap-1">
            <span class="text-xl font-bold text-primary-600 dark:text-primary-400">
                ₱{{ number_format($product->price, 2) }}
            </span>
            @if ($product->original_price > $product->price)
                <span class="text-sm text-zinc-500 line-through">
                    ₱{{ number_format($product->original_price, 2) }}
                </span>
            @endif
        </div>

        <!-- Farmer Info -->
        @if ($farmer)
            <div class="flex items-center gap-2 pt-2 border-t border-primary-100 dark:border-primary-900">
                <div class="flex-shrink-0 h-7 w-7 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">
                        {{ substr($farmer->name, 0, 1) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 truncate">
                        {{ $farmer->name }}
                    </p>
                    @if ($farmer->ratings_average)
                        <div class="flex items-center gap-1">
                            <div class="flex text-yellow-400">
                                @for ($i = 0; $i < 5; $i++)
                                    @if ($i < floor($farmer->ratings_average))
                                        ★
                                    @elseif ($i < $farmer->ratings_average)
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs text-zinc-500">{{ number_format($farmer->ratings_average, 1) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex gap-2 pt-3">
            <a href="{{ route('user.products.show', $product->id) }}" 
                wire:navigate
                class="flex-1 px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors text-center">
                View Details
            </a>
            <button type="button"
                class="px-3 py-2 border border-primary-300 dark:border-primary-700 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.172 4.172a4 4 0 015.656 0L12 6.343m0 0l2.172-2.172a4 4 0 015.656 5.656L12 17.657m0 0l2.172 2.172a4 4 0 01-5.656 5.656L12 17.657" />
                </svg>
            </button>
        </div>
    </div>
</div>
