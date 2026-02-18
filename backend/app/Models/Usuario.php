<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'plano_id',
        'admin',
        'avatar',
        'cpf',
        'data_nascimento',
        'codigo_verificacao',
        'codigo_expira_em'
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'senha' => 'hashed',
            'admin' => 'boolean',
            'data_nascimento' => 'date',
            'codigo_expira_em' => 'datetime',
        ];
    }

    public function lancamentos()
    {
        return $this->hasMany(Lancamento::class, 'user_id');
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'plano_id');
    }

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class, 'user_id');
    }

    public function metas()
    {
        return $this->hasMany(Meta::class, 'usuario_id');
    }

    public function avisos()
    {
        return $this->hasMany(Lembrete::class, 'usuario_id');
    }

    public function convitesEnviados()
    {
        return $this->hasMany(ConviteEnviado::class, 'usuario_id');
    }
}
