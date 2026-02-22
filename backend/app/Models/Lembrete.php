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
        'icone',
        'cor',
        'prazo',
        'hora',
        'notificacao_site',
        'notificacao_email',
        'email_notified_at',
        'site_notified_at',
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
