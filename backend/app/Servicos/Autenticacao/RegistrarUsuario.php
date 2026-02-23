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
            'data_nascimento' => $dados['data_nascimento'],
            'aceita_termos' => $dados['aceita_termos'] ?? false,
            'aceita_notificacoes' => $dados['aceita_notificacoes'] ?? true
        ]);

        app(GerarCodigoVerificacao::class)->executar($usuario);

        return $usuario->load('plano.recursos');
    }
}
