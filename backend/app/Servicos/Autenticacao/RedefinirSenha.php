<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class RedefinirSenha
{
    public function executar(array $dados)
    {
        $usuario = Usuario::where('email', $dados['email'])
            ->where('reset_code', $dados['codigo'])
            ->where('reset_code_expires_at', '>', now())
            ->first();

        if (!$usuario) {
            throw new \Exception('O código de redefinição é inválido ou expirou.', 422);
        }

        $usuario->update([
            'senha' => Hash::make($dados['senha']),
            'reset_code' => null,
            'reset_code_expires_at' => null
        ]);

        return true;
    }
}
