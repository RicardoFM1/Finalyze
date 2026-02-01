<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    use HasFactory;

    protected $table = 'lancamentos';

    protected $fillable = [
        'user_id',
        'tipo',
        'valor',
        'categoria',
        'descricao',
        'data'
    ];

    protected $casts = [
        'data' => 'date',
        'valor' => 'decimal:2'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
