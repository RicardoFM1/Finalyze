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
        'description',
        'max_transactions',
        'is_active'
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'max_transactions' => 'integer'
    ];

    public function periods()
    {
        return $this->belongsToMany(Period::class, 'plan_period')
            ->withPivot('price_cents', 'discount_percentage')
            ->withTimestamps();
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'plan_feature')
            ->withTimestamps();
    }
}
