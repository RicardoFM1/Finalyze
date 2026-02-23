<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class Lancamento extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lancamentos';

    protected $fillable = [
        'user_id',
        'tipo',
        'valor',
        'categoria',
        'descricao',
        'data',
    ];

    protected $casts = [
        'data' => 'date',
        'valor' => 'decimal:2',
    ];

    public static function validarLimiteLancamentos($userId)
    {
        $usuario = Usuario::find($userId);
        if (!$usuario || $usuario->admin) {
            return;
        }

        $limiteLancamentos = Plano::where('id', $usuario->plano_id)->value('limite_lancamentos');
        if ($limiteLancamentos === null) {
            return;
        }

        $lancamentosDoUsuario = self::where('user_id', $userId)->count();
        if ($lancamentosDoUsuario >= (int) $limiteLancamentos) {
            throw ValidationException::withMessages([
                'message' => ['Voce atingiu o limite de lancamentos do plano, atualize ou adquira um novo.'],
            ]);
        }
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
