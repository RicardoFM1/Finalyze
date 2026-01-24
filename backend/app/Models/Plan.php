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
        'price',
        'interval',
        'description',
        'features',
        'max_transactions',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2'
    ];
}
