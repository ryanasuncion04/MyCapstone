<?php

use App\Models\User;
use App\Models\Conversation;

Broadcast::channel('chat.{conversationId}', function (User $user, int $conversationId) {
    $conversation = Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }

    return $user->id === $conversation->user_one_id || $user->id === $conversation->user_two_id;
});

Broadcast::channel('online-users', function (User $user) {
    return ['id' => $user->id, 'name' => $user->name];
});
