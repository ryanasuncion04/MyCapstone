<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

// app/Models/Farmer.php

class Farmer extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'barangay',
        'municipality',
        'latitude',
        'longitude',
        'image',
    ];
        protected static function booted()
    {
        static::deleting(function ($farmer) {
            if ($farmer->image && Storage::disk('public')->exists($farmer->image)) {
                Storage::disk('public')->delete($farmer->image);
            }
        });
    }
    public function farmProduces()
    {
        return $this->hasMany(FarmProduce::class);
    }
}

