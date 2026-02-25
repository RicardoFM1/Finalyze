<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class RegistrarUsuario
{
    public function executar(array $dados)
    {
        $aceitaTermos = filter_var($dados['aceita_termos'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $aceitaNotificacoes = filter_var($dados['aceita_notificacoes'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $usuario = Usuario::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => Hash::make($dados['senha']),
            'cpf' => $dados['cpf'],
            'data_nascimento' => $dados['data_nascimento'],
            'aceita_termos' => $aceitaTermos ? 'true' : 'false',
            'aceita_notificacoes' => $aceitaNotificacoes ? 'true' : 'false'
        ]);

        app(GerarCodigoVerificacao::class)->executar($usuario);

        return $usuario->load('plano.recursos');
    }
}
