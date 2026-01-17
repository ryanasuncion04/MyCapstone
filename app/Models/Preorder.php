<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    protected $fillable = [
        'farm_produce_id',
        'customer_id',
        'quantity',
        'status',
    ];

    public function produce()
    {
        return $this->belongsTo(FarmProduce::class, 'farm_produce_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id'); // assuming customers are users
    }

    /* ======================
     | Status Helpers
     ====================== */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
