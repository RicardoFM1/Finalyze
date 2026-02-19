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
        'token_hash',
        'expira_em',
        'aceito_em',
    ];

    protected function casts(): array
    {
        return [
            'expira_em' => 'datetime',
            'aceito_em' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
