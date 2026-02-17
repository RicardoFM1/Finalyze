<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensagemChat extends Model
{
    protected $table = 'mensagens_chat';

    protected $fillable = [
        'usuario_id',
        'role',
        'texto'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
