<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Farmer;
use App\Models\Preorder;

class FarmProduce extends Model
{
    protected $fillable = [
        'user_id', // manager id
        'farmer_id',

        'product',
        'description',

        'price',
        'quantity',
        'reserved_quantity',

        'image',
        'status',

        // NEW: availability window
        'available_from',
        'available_until',
    ];

    /**
     * ======================
     * CASTS
     * ======================
     */
    protected $casts = [
        'available_from' => 'date',
        'available_until' => 'date',
    ];

    /**
     * ======================
     * RELATIONSHIPS
     * ======================
     */

    // Manager (creator)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Farmer owner
    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    // Preorders
    public function preorders()
    {
        return $this->hasMany(Preorder::class);
    }

    /**
     * ======================
     * BUSINESS LOGIC HELPERS
     * ======================
     */

    // Available stock
    public function availableQuantity(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }

    // Basic availability (legacy logic)
    public function isAvailable(): bool
    {
        return $this->status === 'available'
            && $this->availableQuantity() > 0;
    }

    /**
     * ======================
     * AVAILABILITY WINDOW LOGIC
     * ======================
     */

    // Currently active (BEST for your system)
    public function isCurrentlyActive(): bool
    {
        $now = now();

        return $this->status === 'available'
            && $this->availableQuantity() > 0
            && (!$this->available_from || $now->gte($this->available_from))
            && (!$this->available_until || $now->lte($this->available_until));
    }

    // Not yet available
    public function isUpcoming(): bool
    {
        return $this->available_from && now()->lt($this->available_from);
    }

    // Already expired
    public function isExpired(): bool
    {
        return $this->available_until && now()->gt($this->available_until);
    }

    // Strict availability check (recommended for marketplace use)
    public function isMarketAvailable(): bool
    {
        return $this->isCurrentlyActive();
    }
}
