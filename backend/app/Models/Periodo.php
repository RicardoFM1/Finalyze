<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $table = 'periodos';

    protected $fillable = ['nome', 'slug', 'quantidade_dias'];

    public function planos()
    {
        return $this->belongsToMany(Plano::class, 'plano_periodo', 'periodo_id', 'plano_id')
            ->withPivot('valor_centavos', 'percentual_desconto')
            ->withTimestamps();
    }
}
