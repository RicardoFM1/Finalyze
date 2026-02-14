<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class LoginUsuario
{
    public function executar(array $credenciais)
    {
        $authCredenciais = [
            'email' => $credenciais['email'],
            'password' => $credenciais['senha']
        ];

        if (!Auth::attempt($authCredenciais)) {
            throw new \Exception('Credenciais inválidas. Verifique seu e-mail e senha.', 401);
        }

        $usuario = Usuario::where('email', $credenciais['email'])->firstOrFail();
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario->load('plano.recursos')
        ];
    }
}
