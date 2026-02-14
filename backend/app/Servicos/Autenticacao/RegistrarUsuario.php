<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class RegistrarUsuario
{
    public function executar(array $dados)
    {
        $usuario = Usuario::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => Hash::make($dados['senha']),
            'cpf' => $dados['cpf'],
            'data_nascimento' => $dados['data_nascimento']
        ]);

        app(GerarCodigoVerificacao::class)->executar($usuario);

        return $usuario->load('plano.recursos');
    }
}
