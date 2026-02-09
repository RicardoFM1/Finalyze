<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LembreteEnviado extends Model
{
    use HasFactory;

    protected $table = 'lembretes_enviados';

    protected $fillable = [
        'assinatura_id',
        'tipo',
        'enviado_em'
    ];

    protected $casts = [
        'enviado_em' => 'datetime'
    ];

    public function assinatura()
    {
        return $this->belongsTo(Assinatura::class, 'assinatura_id');
    }
}
