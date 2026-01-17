<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;
use Livewire\Attributes\On;

class ChatWindow extends Component
{
    public Conversation $conversation;
    public string $messageBody = '';
    public array $messages = [];

    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation;
        $this->loadMessages();
        
        // Subscribe to messages
        $this->dispatch('echo:chat.' . $conversation->id . ',MessageSent', []);
    }

    public function loadMessages()
    {
        $this->messages = $this->conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function sendMessage()
    {
        if (blank($this->messageBody)) {
            return;
        }

        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => auth()->id(),
            'body' => $this->messageBody,
        ]);

        // Update last message timestamp
        $this->conversation->update(['last_message_at' => now()]);

        $this->messageBody = '';
        $this->loadMessages();

        // Broadcast to the other user
        broadcast(new \App\Events\MessageSent($message))->toOthers();
    }

    #[On('echo:chat.{conversation.id},MessageSent')]
    public function onMessageSent()
    {
        $this->loadMessages();
    }

    public function getOtherUser()
    {
        return $this->conversation->getOtherUser(auth()->id());
    }

    public function render()
    {
        return view('livewire.chat.chat-window');
    }
}
