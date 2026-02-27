<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colaboracao extends Model
{
    protected $table = 'colaboracoes';

    protected $fillable = [
        'proprietario_id',
        'email_convidado',
        'status'
    ];

    public function proprietario()
    {
        return $this->belongsTo(Usuario::class, 'proprietario_id');
    }

    public function convidado()
    {
        return $this->belongsTo(Usuario::class, 'email_convidado', 'email');
    }

    public function owner()
    {
        return $this->proprietario();
    }
    public function guest()
    {
        return $this->convidado();
    }
}
