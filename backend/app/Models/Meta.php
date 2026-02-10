<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $fillable = [
        'usuario_id',
        'titulo',
        'descricao',
        'tipo',
        'valor_objetivo',
        'valor_objetivo',
        'meta_quantidade',
        'atual_quantidade',
        'unidade',
        'prazo',
        'status',
        'cor',
        'icone'
    ];

    protected $casts = [
        'valor_objetivo' => 'float',
        'valor_objetivo' => 'float',
        'meta_quantidade' => 'integer',
        'atual_quantidade' => 'integer',
        'prazo' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
