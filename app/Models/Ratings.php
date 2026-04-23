<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Farmer;
use App\Models\User;    
use App\Models\Preorder;
class Ratings extends Model
{
    protected $fillable = [
        'customer_id',
        'farmer_id',
        'preorder_id',
        'rating',
        'comment',
    ];

     public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function preorder()
    {
        return $this->belongsTo(Preorder::class);
    }
}
