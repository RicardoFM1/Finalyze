<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

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

    public static function validarLimiteLancamentos($userId){
        $lancamentoUserCount = Lancamento::where('user_id', $userId)->count();
        $userPlanoId = Usuario::where('id', $userId)->value('plano_id');
        $userPlanoLimiteLancamentos = Plano::where('id', $userPlanoId)->value('limite_lancamentos');
        if($lancamentoUserCount >= $userPlanoLimiteLancamentos){
            throw ValidationException::withMessages([
                'message' => ['Você atingiu o limite de lançamentos do plano, atualize ou adquira um novo.']
            ]);
        }
        
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
