<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'faturamentos';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount_cents',
        'status',
        'payment_method',
        'mercado_pago_id',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
