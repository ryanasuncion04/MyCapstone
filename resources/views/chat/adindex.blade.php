<x-layouts.adapp>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold">Messages</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Search Bar -->
            <div class="lg:col-span-3">
                <form method="GET" action="{{ route('chat.adindex') }}" class="flex gap-2 mb-4">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Search conversations and users..."
                        class="flex-1 border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-800 dark:text-white"
                    />
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Search</button>
                    @if(!empty($search))
                        <a href="{{ route('chat.mindex') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">Clear</a>
                    @endif
                </form>
            </div>
            <!-- Conversations List -->
            <div class="lg:col-span-1 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-lg font-bold">Conversations @if(!empty($search)) ({{ $conversations->count() }}) @endif</h2>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700 max-h-96 overflow-y-auto">
                    @forelse ($conversations as $conversation)
                        <a href="{{ route('chat.adshow', $conversation) }}" 
                           class="block p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            <div class="font-medium">{{ $conversation->getOtherUser(auth()->id())->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate mt-1">
                                {{ $conversation->messages()->latest()->first()?->body ?? 'No messages yet' }}
                            </div>
                            @if ($conversation->last_message_at)
                                <div class="text-xs text-gray-400 mt-1">{{ $conversation->last_message_at->diffForHumans() }}</div>
                            @endif
                        </a>
                    @empty
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            No conversations yet
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- All Users -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-lg font-bold">All Users @if(!empty($search)) ({{ $allUsers->count() }}) @endif</h2>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($allUsers as $user)
                        <div class="flex items-center justify-between p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            <div>
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </div>
                            <form action="{{ route('chat.adstart') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                                    Message
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            No other users available
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.adapp>
