<x-layouts.mapp title="Manage Preorders">
    <div class="flex justify-between mb-4">
        <h1 class="text-xl font-semibold">Preorders</h1>
    </div>

    <div class="rounded-xl border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800">
                <tr>
                    <th class="p-2">Customer</th>
                    <th class="p-2">Produce</th>
                    <th class="p-2">Farmer</th>
                    <th class="p-2">Quantity</th>
                    <th class="p-2">Status</th>
                    <th class="text-right p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preorders as $preorder)
                <tr class="border-t">
                    <td class="p-2">{{ $preorder->customer->name }}</td>
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
                    <td class="text-right p-2 space-x-2">
                        @if($preorder->status === 'pending')
                            <form method="POST" action="{{ route('manager.preorders.approve', $preorder) }}" class="inline">
                                @csrf
                                <button class="px-2 py-1 bg-green-600 text-white rounded text-xs">Approve</button>
                            </form>

                            <form method="POST" action="{{ route('manager.preorders.reject', $preorder) }}" class="inline">
                                @csrf
                                <button class="px-2 py-1 bg-red-600 text-white rounded text-xs">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.mapp>
