<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use App\Models\Conversation;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    // Return initials like "JD" for "John Doe"
    public function initials(): string
    {
        $name = $this->name ?? $this->email ?? '';
        $parts = preg_split('/\s+/', trim($name));
        if (!$parts || $parts[0] === '') {
            return '';
        }
        if (count($parts) === 1) {
            return strtoupper(mb_substr($parts[0], 0, 1));
        }
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
    }

    // Allow $user->initials usage
    public function getInitialsAttribute(): string
    {
        return $this->initials();
    }

    // Relationships for conversations
    public function conversationsAsUserOne()
    {
        return $this->hasMany(Conversation::class, 'user_one_id');
    }

    public function conversationsAsUserTwo()
    {
        return $this->hasMany(Conversation::class, 'user_two_id');
    }

    // Get all conversations for this user
    public function getConversations()
    {
        return Conversation::where('user_one_id', $this->id)
            ->orWhere('user_two_id', $this->id)
            ->with(['userOne', 'userTwo', 'messages'])
            ->orderBy('last_message_at', 'desc')
            ->get();
    }
}
