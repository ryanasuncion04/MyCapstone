@props(['title', 'value', 'icon' => null, 'trend' => null, 'color' => 'primary'])

<div class="rounded-xl bg-white dark:bg-zinc-800 shadow-sm border border-{{ $color }}-100 dark:border-{{ $color }}-900 p-6 hover:shadow-md transition-shadow">
    
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $title }}</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $value }}</p>
                @if ($trend)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                        {{ $trend >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' }}">
                        {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                    </span>
                @endif
            </div>
        </div>
        
        @if ($icon)
            <div class="flex-shrink-0 rounded-lg bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 p-3">
                {!! $icon !!}
            </div>
        @endif
    </div>

    {{ $slot }}
</div>
