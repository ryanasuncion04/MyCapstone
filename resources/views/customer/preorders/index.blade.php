<x-layouts.app title="My Preorders">
    <div class="mb-4">
        <h1 class="text-xl font-semibold">My Preorders</h1>
    </div>

    <div class="rounded-xl border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800">
                <tr>
                    <th class="p-2">Produce</th>
                    <th class="p-2">Farmer</th>
                    <th class="p-2">Quantity</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preorders as $preorder)
                <tr class="border-t">
                    <td class="p-2">{{ $preorder->produce->product }}</td>
                    <td class="p-2">{{ $preorder->produce->farmer->name }}</td>
                    <td class="p-2">{{ $preorder->quantity }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 rounded text-xs 
                            @if($preorder->status === 'pending')
                            @elseif($preorder->status === 'approved') 
                            @elseif($preorder->status === 'rejected') 
                            @endif
                        ">
                            {{ ucfirst($preorder->status) }}
                        </span>
                    </td>
                    <td class="p-2">
                        @if($preorder->status === 'pending')
                            <form method="POST" action="{{ route('customer.preorders.cancel', $preorder) }}">
                                @csrf
                                @method('DELETE')
                                <button class="px-2 py-1 bg-red-500 text-white rounded text-xs">Cancel</button>
                            </form>
                        @else
                            <span class="text-xs text-gray-500">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
