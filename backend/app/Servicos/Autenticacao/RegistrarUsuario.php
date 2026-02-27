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

        $dataNascimento = new \DateTime($dados['data_nascimento']);
        $hoje = new \DateTime();
        $idade = $hoje->diff($dataNascimento)->y;

        if ($idade < 18) {
            throw new \Exception('Você deve ter pelo menos 18 anos para usar este serviço.', 422);
        }

        $domain = substr(strrchr($dados['email'], "@"), 1);
        $blockedDomains = [
            'mailinator.com',
            'trashmail.com',
            'tempmail.com',
            'guerrillamail.com',
            'sharklasers.com',
            '10minutemail.com',
            'yopmail.com',
            'dispostable.com',
            'getnada.com',
            'temp-mail.org',
            'internal.ml',
            'dropmail.me'
        ];

        if (in_array(strtolower($domain), $blockedDomains)) {
            throw new \Exception('O uso de e-mails temporários não é permitido para este serviço.', 422);
        }

        if (Usuario::where('cpf', $dados['cpf'])->exists()) {
            throw new \Exception('Este CPF já está cadastrado no sistema.', 422);
        }

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
