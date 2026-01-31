<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'days_count'];

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_period')
            ->withPivot('price_cents', 'discount_percentage')
            ->withTimestamps();
    }
}
