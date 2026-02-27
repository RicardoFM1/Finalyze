<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class VerificarCodigo
{
    public function executar(string $email, string $codigo)
    {
        $usuario = Usuario::where('email', $email)
            ->where('codigo_verificacao', $codigo)
            ->where('codigo_expira_em', '>', now())
            ->first();

        if (!$usuario) {
            throw new \Exception('Código inválido ou expirado.', 422);
        }

        $usuario->update([
            'codigo_verificacao' => null,
            'codigo_expira_em' => null,
            'email_verified_at' => now()
        ]);

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario->load('plano.recursos')
        ];
    }
}
