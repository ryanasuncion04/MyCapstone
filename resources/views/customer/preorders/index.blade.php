<x-layouts.app title="My Preorders">
    <div class="space-y-6 p-6 max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
                My Preorders
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400">
                Track your farm product orders and farmer ratings
            </p>
        </div>

        <!-- Preorders Table -->
        <div class="rounded-xl border border-primary-200 dark:border-primary-900 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30 border-b border-primary-200 dark:border-primary-900">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Produce</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Farmer</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Contact</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Quantity</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Status</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Actions</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Rating</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-primary-100 dark:divide-primary-900">
                        @foreach ($preorders as $preorder)
                            <tr class="hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-colors">

                                <td class="px-6 py-4">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $preorder->produce->product }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $preorder->produce->farmer->name }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <a href="tel:{{ $preorder->produce->farmer->contact }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                        {{ $preorder->produce->farmer->contact }}
                                    </a>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $preorder->quantity }} kg</span>
                                </td>

                                <!-- STATUS -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if ($preorder->status === 'pending') 
                                            bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200
                                        @elseif($preorder->status === 'approved') 
                                            bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200
                                        @elseif($preorder->status === 'rejected') 
                                            bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200 
                                        @endif
                                    ">
                                        ● {{ ucfirst($preorder->status) }}
                                    </span>
                                </td>

                                <!-- ACTIONS -->
                                <td class="px-6 py-4">
                                    @if ($preorder->status === 'pending')
                                        <form method="POST" action="{{ route('customer.preorders.cancel', $preorder) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-200 rounded-lg text-xs font-medium transition-colors">
                                                Cancel
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-zinc-500">—</span>
                                    @endif
                                </td>

                                <!-- RATING -->
                                <td class="px-6 py-4">
                                    @if ($preorder->status === 'approved' && !$preorder->rating)
                                        <form method="POST" action="{{ route('user.preorders.rate', $preorder) }}"
                                            class="space-y-2">

                                            @csrf

                                            <!-- STAR RATING -->
                                            <div class="flex items-center gap-2">
                                                <div class="flex flex-row-reverse gap-1">
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <input type="radio" id="star{{ $preorder->id }}-{{ $i }}"
                                                            name="rating" value="{{ $i }}" class="hidden peer">

                                                        <label for="star{{ $preorder->id }}-{{ $i }}"
                                                            class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 transition text-lg">
                                                            ★
                                                        </label>
                                                    @endfor
                                                </div>

                                                <button type="button" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 text-xs font-medium" onclick="document.getElementById('comment-{{ $preorder->id }}').classList.toggle('hidden')">
                                                    Comment
                                                </button>
                                            </div>

                                            <!-- COMMENT (Hidden by default) -->
                                            <textarea name="comment" rows="2" placeholder="Optional feedback..."
                                                class="hidden w-full text-xs border border-primary-300 dark:border-primary-700 rounded-lg p-2 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary-500"
                                                id="comment-{{ $preorder->id }}"></textarea>

                                            <!-- SUBMIT -->
                                            <button type="submit"
                                                class="w-full px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-medium transition-colors">
                                                Submit Rating
                                            </button>

                                        </form>
                                    @elseif($preorder->rating)
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg border border-yellow-200 dark:border-yellow-900 text-xs">
                                            <div class="text-yellow-600 dark:text-yellow-400 font-semibold flex items-center gap-1">
                                                ⭐ {{ $preorder->rating->rating }}/5
                                            </div>

                                            @if ($preorder->rating->comment)
                                                <p class="text-zinc-600 dark:text-zinc-400 mt-1">
                                                    "{{ $preorder->rating->comment }}"
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-zinc-500">—</span>
                                    @endif

                                </td>


                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
