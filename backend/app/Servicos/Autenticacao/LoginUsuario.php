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

        app(GerarCodigoVerificacao::class)->executar($usuario);

        return [
            'requer_verificacao' => true,
            'email' => $usuario->email
        ];
    }
}
