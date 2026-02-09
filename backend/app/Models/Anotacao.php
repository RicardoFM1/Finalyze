<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anotacao extends Model
{
    use SoftDeletes;

    protected $table = 'anotacoes';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'descricao',
        'icone',
        'cor',
        'prazo',
        'status'
    ];

    protected $casts = [
        'prazo' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
