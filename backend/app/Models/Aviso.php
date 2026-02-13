<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aviso extends Model
{
    use SoftDeletes;

    protected $table = 'avisos';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'descricao',
        'categoria',
        'cor',
        'data_aviso',
        'status',
    ];

    protected $casts = [
        'data_aviso' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
