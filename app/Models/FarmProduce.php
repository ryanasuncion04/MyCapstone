<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/FarmProduce.php

class FarmProduce extends Model
{
    protected $fillable = [
        'farmer_id',
        'product',
        'description',
        'price', 
        'quantity',
        'reserve_quantity',
        'image',
        'status',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function preorders()
    {
        return $this->hasMany(Preorder::class);
    }

    /* ======================
     | Helpers
     ====================== */

    public function availableQuantity(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->availableQuantity() > 0;
    }
}

