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

    public function LimiteLancamentos($userId){
        $lancamentoUserCount = Lancamento::where('user_id', $userId)->count();
        $userPlanoId = Usuario::where('id', $userId)->value('plano_id');
        $userPlanoLimiteLancamentos = Plano::where('id', $userPlanoId)->first()->value('limite_lancamentos');
        if($lancamentoUserCount >= $userPlanoLimiteLancamentos){
            return true;
        }
        return false;
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
