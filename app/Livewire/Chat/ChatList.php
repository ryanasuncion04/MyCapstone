<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Livewire\Component;

class ChatList extends Component
{
    public array $conversations = [];

    public function mount()
    {
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $this->conversations = auth()->user()
            ->getConversations()
            ->map(function ($conversation) {
                return [
                    'id' => $conversation->id,
                    'otherUser' => $conversation->getOtherUser(auth()->id()),
                    'lastMessage' => $conversation->messages()->latest()->first()?->body,
                    'lastMessageAt' => $conversation->last_message_at,
                ];
            })
            ->toArray();
    }

    public function startConversation($userId)
    {
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('user_one_id', auth()->id())
                ->where('user_two_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)
                ->where('user_two_id', auth()->id());
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => auth()->id(),
                'user_two_id' => $userId,
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function render()
    {
        return view('livewire.chat.chat-list');
    }
}
