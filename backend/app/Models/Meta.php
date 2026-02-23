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
        'meta_quantidade',
        'atual_quantidade',
        'unidade',
        'prazo',
        'status',
        'cor',
        'icone',
        'notificacao_site',
        'notificacao_email',
        'email_notified_at'
    ];

    protected $casts = [
        'valor_objetivo' => 'float',
        'meta_quantidade' => 'integer',
        'atual_quantidade' => 'integer',
        'prazo' => 'date',
        'notificacao_site' => 'boolean',
        'notificacao_email' => 'boolean',
        'email_notified_at' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
