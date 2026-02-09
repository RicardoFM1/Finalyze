<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{
    use HasFactory;

    protected $table = 'recursos';

    protected $fillable = ['nome', 'slug', 'descricao'];

    public function planos()
    {
        return $this->belongsToMany(Plano::class, 'plano_recurso', 'recurso_id', 'plano_id')
            ->withTimestamps();
    }
}
