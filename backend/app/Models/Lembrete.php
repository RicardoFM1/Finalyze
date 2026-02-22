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
<<<<<<< HEAD
        'meta_id',
        'titulo',
        'descricao',
        'categoria',
        'cor',
        'data_aviso',
        'data_lembrete',
        'status',
    ];

    protected $casts = [
        'data_aviso'    => 'datetime',
        'data_lembrete' => 'datetime',
=======
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
>>>>>>> Ricardo
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
