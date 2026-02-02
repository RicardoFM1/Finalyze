<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    use HasFactory;

    protected $table = 'assinaturas';

    protected $fillable = [
        'user_id',
        'plano_id',
        'periodo_id',
        'mercado_pago_id',
        'status',
        'inicia_em',
        'termina_em',
        'renovacao_automatica'
    ];

    protected $casts = [
        'inicia_em' => 'datetime',
        'termina_em' => 'datetime',
        'renovacao_automatica' => 'boolean'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'plano_id');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }
}
