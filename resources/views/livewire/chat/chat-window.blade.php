<div class="flex flex-col h-full bg-white dark:bg-zinc-900">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 p-4">
        <h3 class="text-lg font-bold">{{ $this->getOtherUser()?->name ?? 'Chat' }}</h3>
    </div>

    <!-- Messages -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4" wire:poll="loadMessages">
        @if (count($messages) === 0)
            <div class="flex items-center justify-center h-full text-gray-500">
                <p>No messages yet. Start the conversation!</p>
            </div>
        @else
            @foreach ($messages as $msg)
                <div class="flex @if ($msg['user_id'] === auth()->id()) justify-end @endif">
                    <div class="max-w-xs @if ($msg['user_id'] === auth()->id()) bg-blue-500 text-white @else bg-gray-200 dark:bg-zinc-700 @endif rounded-lg p-3">
                        <p>{{ $msg['body'] }}</p>
                        <p class="text-xs @if ($msg['user_id'] === auth()->id()) text-blue-100 @else text-gray-500 dark:text-gray-400 @endif mt-1">
                            {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Input -->
    <div class="bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700 p-4">
        <form wire:submit="sendMessage" class="flex gap-2">
            <input 
                type="text" 
                wire:model="messageBody" 
                placeholder="Type a message..." 
                class="flex-1 border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-800 dark:text-white"
            />
            <button 
                type="submit" 
                class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600"
            >
                Send
            </button>
        </form>
    </div>
</div>
