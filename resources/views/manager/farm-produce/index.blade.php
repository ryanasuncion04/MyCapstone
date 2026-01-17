{{-- resources/views/manager/farm-produce/index.blade.php --}}

<x-layouts.mapp :title="__('Farm Produce Management')">
    <div class="flex h-full w-full flex-col gap-4">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-3">
            <h1 class="text-xl font-semibold">Farm Produce</h1>

            <a href="{{ route('manager.farm-produce.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-white hover:opacity-90"
                wire:navigate>
                + Add Produce
            </a>
        </div>

        {{-- Table --}}
        <div class="rounded-xl border overflow-hidden bg-white dark:bg-zinc-900">
            <table class="w-full text-sm">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="p-3 text-left">Image</th>
                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-left">Description</th>
                        <th class="p-3 text-left">Quantity</th>
                        <th class="p-3 text-left">Price</th>
                        <th class="p-3 text-left">Status</th>
                         <th class="p-3 text-left">Farmer</th>
                        <th class="p-3 text-left">Contact</th>
                        <th class="p-3 text-left">Municipality</th>
                        <th class="p-3 text-left">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($produces as $produce)
                        <tr class="border-t">
                            {{-- Image --}}
                            <td class="p-3">
                                @if ($produce->image)
                                    <img src="{{ Storage::url($produce->image) }}" alt="{{ $produce->product }}"
                                        class="h-12 w-12 rounded object-cover">
                                @else
                                    <div class="h-12 w-12 rounded bg-zinc-200 dark:bg-zinc-700"></div>
                                @endif
                            </td>

                            {{-- Product --}}
                            <td class="p-3 font-medium">
                                {{ $produce->product }}
                            </td>

                            {{-- Description --}}
                            <td class="p-3 text-zinc-600 dark:text-zinc-400">
                                {{ $produce->description ?? '—' }}
                            </td>

                            {{-- Quantity --}}
                            <td class="p-3">
                                {{ $produce->quantity }}
                            </td>
                            {{-- Price --}}
                            <td class="p-3">
                                {{ $produce->price }}
                            </td>

                            {{-- Status --}}
                            <td class="p-3">
                                {{ $produce->status }}
                            </td>
                            
                            {{-- Farmer --}}
                            <td class="p-3">
                                {{ $produce->farmer->name }}
                            </td>

                            {{-- Farmer Contact--}}
                            <td class="p-3">
                                {{ $produce->farmer->contact }}
                            </td>

                            {{-- Municipality --}}
                            <td class="p-3">
                                {{ $produce->farmer->municipality }}
                            </td>

                            {{-- Actions --}}
                            <td class="p-3 text-left whitespace-nowrap">
                                <a href="{{ route('manager.farm-produce.edit', $produce) }}"
                                    class="text-primary hover:underline" wire:navigate>
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('manager.farm-produce.destroy', $produce) }}"
                                    class="inline" onsubmit="return confirm('Delete this produce?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="ml-2 text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-zinc-500">
                                No farm produce found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.mapp>
