<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'plan_id',
        'subscription_status',
        'avatar',
        'cpf',
        'birth_date'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    protected $appends = ['birth_date_formatted'];

    public function getBirthDateFormattedAttribute()
    {
        return $this->birth_date ? $this->birth_date->format('d/m/Y') : null;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
