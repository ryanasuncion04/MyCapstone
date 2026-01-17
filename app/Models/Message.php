<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;

class Message extends Model
{
    use HasFactory, BroadcastsEvents;

    protected $fillable = ['conversation_id', 'user_id', 'body'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function broadcastOn($events = null)
    {
        return [
            new PrivateChannel('chat.' . $this->conversation_id),
        ];
    }
}
