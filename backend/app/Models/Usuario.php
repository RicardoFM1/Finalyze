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
        'codigo_expira_em',
        'idioma'
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $appends = ['avatar_url'];

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

    public function historicosPagamento()
    {
        return $this->hasMany(HistoricoPagamento::class, 'user_id');
    }

    public function assinaturaAtiva()
    {
        return $this->assinaturas()
            ->where('status', 'active')
            ->where('termina_em', '>=', now())
            ->orderByDesc('termina_em')
            ->first();
    }


    public function metas()
    {
        return $this->hasMany(Meta::class, 'usuario_id');
    }

    public function lembretes()
    {
        return $this->hasMany(Lembrete::class, 'usuario_id');
    }

    public function shares()
    {
        return $this->hasMany(AccountShare::class, 'owner_id');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar;
    }
}
