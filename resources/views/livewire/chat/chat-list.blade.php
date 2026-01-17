<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold">All Users</h3>
    </div>

    <div class="space-y-2">
        @forelse ($conversations as $conv)
            <div class="flex items-center justify-between p-3 border rounded-lg">
                <span class="font-medium">{{ $conv['otherUser']->name }}</span>
                <button 
                    wire:click="startConversation({{ $conv['otherUser']->id }})"
                    class="text-blue-500 hover:text-blue-700 text-sm"
                >
                    Message
                </button>
            </div>
        @empty
            <p class="text-gray-500">No conversations yet</p>
        @endforelse
    </div>
</div>
