<x-layouts.app>
    <div class="flex h-screen bg-white dark:bg-zinc-900 -mx-6 -my-6">
        <!-- Conversations List -->
        <div class="w-80 border-r border-zinc-200 dark:border-zinc-700 flex flex-col">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <a href="{{ route('chat.index') }}" class="text-blue-500 hover:text-blue-700 mb-4 inline-block">
                    ← Back
                </a>
                <h2 class="text-xl font-bold">Messages</h2>
            </div>

            <div class="flex-1 overflow-y-auto">
                @forelse ($conversations as $conv)
                    <a href="{{ route('chat.show', $conv) }}" 
                       class="block p-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 @if($conv->id === $conversation->id) bg-blue-50 dark:bg-zinc-800 @endif">
                        <div class="font-medium">{{ $conv->getOtherUser(auth()->id())->name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $conv->messages()->latest()->first()?->body ?? 'No messages yet' }}</div>
                        @if ($conv->last_message_at)
                            <div class="text-xs text-gray-400 mt-1">{{ $conv->last_message_at->diffForHumans() }}</div>
                        @endif
                    </a>
                @empty
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        No conversations yet
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Window -->
        <div class="flex-1 flex flex-col">
            @livewire('chat.chat-window', ['conversation' => $conversation])
        </div>
    </div>
</x-layouts.app>
