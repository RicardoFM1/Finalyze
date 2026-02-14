<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConviteEnviado extends Model
{
    use SoftDeletes;

    protected $table = 'convites_enviados';

    protected $fillable = [
        'usuario_id',
        'email_destino',
        'mensagem',
        'status',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
