<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'planos';


    protected $fillable = [
        'name',
        'price_cents',
        'interval',
        'description',
        'features',
        'max_transactions',
        'is_active'
    ];


    protected $casts = [
        'features' => 'array',
        'price_cents' => 'integer'
    ];

    public function getPriceAttribute()
    {
        return $this->price_cents / 100;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price_cents'] = (int)round($value * 100);
    }
}
