<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lembrete extends Model
{
    use SoftDeletes;

    protected $table = 'lembretes';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'descricao',
        'categoria',
        'cor',
        'data_aviso',
        'data_lembrete',
        'status',
    ];

    protected $casts = [
        'data_aviso' => 'datetime',
        'data_lembrete' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
