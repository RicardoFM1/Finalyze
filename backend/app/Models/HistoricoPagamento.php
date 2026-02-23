<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoPagamento extends Model
{
    protected $table = 'historico_pagamentos';

    protected $fillable = [
        'user_id',
        'assinatura_id',
        'valor_centavos',
        'status',
        'metodo_pagamento',
        'mercado_pago_id',
        'pago_em'
    ];

    protected $casts = [
        'pago_em' => 'datetime',
    ];

    public function assinatura()
    {
        return $this->belongsTo(Assinatura::class, 'assinatura_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
