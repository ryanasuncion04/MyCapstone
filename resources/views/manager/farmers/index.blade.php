{{-- resources/views/manager/farmers/index.blade.php --}}

<x-layouts.mapp title="Farmers">
    <div class="flex justify-between mb-4">
        <h1 class="text-xl font-semibold">Pickup-Point</h1>

        <a href="{{ route('manager.farmers.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg" wire:navigate>
            + Pickup-Point
        </a>
    </div>

    <div class="rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800">
                <tr>
                    <th class="p-2 text-left">Name</th>
                    <th class="p-2 text-left">Contact</th>
                    <th class="p-2 text-left">Municipality</th>
                    <th class="p-2 text-left">Location</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($farmers as $farmer)
                    <tr class="border-t">
                        <td class="p-2">{{ $farmer->name }}</td>
                        <td class="p-2">{{ $farmer->contact }}</td>
                        <td class="p-2">{{ $farmer->municipality }}</td>
                        <td class="p-2">{{ $farmer->latitude }}, {{ $farmer->longitude }}</td>
                        <td class="p-2 text-left">
                            <a href="{{ route('manager.farmers.edit', $farmer) }}" wire:navigate>Edit</a>
                            <form class="inline" method="POST"
                                action="{{ route('manager.farmers.destroy', $farmer) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-600 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.mapp>
