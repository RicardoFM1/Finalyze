<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;

    protected $table = 'planos';

    protected $fillable = [
        'nome',
        'descricao',
        'limite_lancamentos',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'limite_lancamentos' => 'integer'
    ];

    public function periodos()
    {
        return $this->belongsToMany(Periodo::class, 'plano_periodo', 'plano_id', 'periodo_id')
            ->withPivot('valor_centavos', 'percentual_desconto')
            ->withTimestamps();
    }

    public function recursos()
    {
        return $this->belongsToMany(Recurso::class, 'plano_recurso', 'plano_id', 'recurso_id')
            ->withTimestamps();
    }
}
