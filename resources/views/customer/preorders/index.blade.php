<x-layouts.app title="My Preorders">
    <div class="mb-4">
        <h1 class="text-xl font-semibold">My Preorders</h1>
    </div>

    <div class="rounded-xl border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800">
                <tr>
                    <th class="p-2 text-left">Produce</th>
                    <th class="p-2 text-left">Farmer</th>
                    <th class="p-2 text-left">Contact</th>
                    <th class="p-2 text-left">Quantity</th>
                    <th class="p-2 text-left">Status</th>
                    <th class="p-2 text-left">Actions</th>
                    <th class="p-2 text-left">Rating</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($preorders as $preorder)
                    <tr class="border-t">

                        <td class="p-2">{{ $preorder->produce->product }}</td>

                        <td class="p-2">{{ $preorder->produce->farmer->name }}</td>

                        <td class="p-2">{{ $preorder->produce->farmer->contact }}</td>

                        <td class="p-2">{{ $preorder->quantity }}</td>

                        {{-- STATUS --}}
                        <td class="p-2">
                            <span
                                class="px-2 py-1 rounded text-xs
                                @if ($preorder->status === 'pending') bg-yellow-200
                                @elseif($preorder->status === 'approved') bg-green-200
                                @elseif($preorder->status === 'rejected') bg-red-200 @endif
                            ">
                                {{ ucfirst($preorder->status) }}
                            </span>
                        </td>

                        {{-- ACTIONS --}}
                        <td class="p-2">
                            @if ($preorder->status === 'pending')
                                <form method="POST" action="{{ route('customer.preorders.cancel', $preorder) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-500 text-white rounded text-xs">
                                        Cancel
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-500">—</span>
                            @endif
                        </td>

                        {{-- RATING --}}
                        <td class="p-2">
                            @if ($preorder->status === 'approved' && !$preorder->rating)
                                <form method="POST" action="{{ route('user.preorders.rate', $preorder) }}"
                                    class="space-y-2 bg-zinc-50 dark:bg-zinc-800 p-2 rounded-lg border">

                                    @csrf

                                    {{-- ⭐ STAR RATING --}}
                                    <div class="flex flex-row-reverse justify-end gap-1">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $preorder->id }}-{{ $i }}"
                                                name="rating" value="{{ $i }}" class="hidden peer">

                                            <label for="star{{ $preorder->id }}-{{ $i }}"
                                                class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 text-lg transition">
                                                ★
                                            </label>
                                        @endfor
                                    </div>

                                    {{-- 💬 COMMENT --}}
                                    <textarea name="comment" rows="2" placeholder="Write your feedback..."
                                        class="w-full text-xs border rounded p-1 focus:ring focus:ring-blue-300 dark:bg-zinc-700"></textarea>

                                    {{-- SUBMIT --}}
                                    <button
                                        class="w-full px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs transition">
                                        Submit Rating
                                    </button>

                                </form>
                            @elseif($preorder->rating)
                                <div class="bg-yellow-50 dark:bg-zinc-800 p-2 rounded-lg border text-xs">
                                    <div class="text-yellow-600 font-semibold">
                                        ⭐ {{ $preorder->rating->rating }}/5
                                    </div>

                                    @if ($preorder->rating->comment)
                                        <p class="text-gray-600 dark:text-gray-300 mt-1">
                                            "{{ $preorder->rating->comment }}"
                                        </p>
                                    @endif
                                </div>
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
