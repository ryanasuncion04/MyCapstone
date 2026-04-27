@props(['farmer', 'productsCount' => 0])

<div class="group rounded-xl overflow-hidden bg-white dark:bg-zinc-800 shadow-sm hover:shadow-md transition-all duration-300 border border-primary-100 dark:border-primary-900 hover:border-primary-300 dark:hover:border-primary-700">
    
    <!-- Header with Background -->
    <div class="relative h-24 bg-gradient-to-r from-primary-500 to-primary-600 dark:from-primary-700 dark:to-primary-800 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" fill="currentColor" viewBox="0 0 100 100">
                <circle cx="20" cy="20" r="15" opacity="0.1"/>
                <circle cx="80" cy="80" r="20" opacity="0.1"/>
                <path d="M0 50 Q 25 40, 50 50 T 100 50" opacity="0.1" stroke="currentColor" fill="none"/>
            </svg>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4 space-y-3">
        
        <!-- Avatar & Name -->
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 -mt-12 h-16 w-16 rounded-xl bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center shadow-md ring-2 ring-white dark:ring-zinc-800">
                @if ($farmer->profile_photo_path)
                    <img src="{{ asset('storage/' . $farmer->profile_photo_path) }}" alt="{{ $farmer->name }}"
                        class="w-full h-full object-cover rounded-xl">
                @else
                    <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ substr($farmer->name, 0, 1) }}
                    </span>
                @endif
            </div>
            
            <div class="flex-1 pt-2">
                <h3 class="font-semibold text-base text-zinc-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                    {{ $farmer->name }}
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $farmer->municipality ?? 'Ilocos Norte' }}
                </p>
            </div>
        </div>

        <!-- Rating -->
        @if ($farmer->ratings && count($farmer->ratings) > 0)
            <div class="flex items-center gap-2 pt-1">
                <div class="flex text-yellow-400">
                    @php
                        $avgRating = $farmer->ratings->avg('rating') ?? 0;
                    @endphp
                    @for ($i = 0; $i < 5; $i++)
                        @if ($i < floor($avgRating))
                            ★
                        @elseif ($i < $avgRating)
                            ⭐
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    {{ number_format($avgRating, 1) }}
                </span>
                <span class="text-xs text-zinc-500">
                    ({{ count($farmer->ratings) }} reviews)
                </span>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-2 py-2 px-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
            <div class="text-center">
                <p class="text-xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $productsCount }}
                </p>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Products</p>
            </div>
            <div class="text-center">
                <p class="text-xl font-bold text-harvest-600">
                    {{ $farmer->experience_years ?? 0 }}
                </p>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Years</p>
            </div>
        </div>

        <!-- Description -->
        @if ($farmer->description)
            <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">
                {{ $farmer->description }}
            </p>
        @endif

        <!-- Action Buttons -->
        <div class="flex gap-2 pt-2 border-t border-primary-100 dark:border-primary-900">
            <a href="{{ route('user.farmers.show', $farmer->id) ?? '#' }}" 
                class="flex-1 px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors text-center">
                View Profile
            </a>
            <a href="{{ route('chat.index') }}" 
                class="px-3 py-2 border border-primary-300 dark:border-primary-700 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </a>
        </div>
    </div>
</div>
