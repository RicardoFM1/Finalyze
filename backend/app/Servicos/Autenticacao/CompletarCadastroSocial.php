<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use Laravel\Sanctum\PersonalAccessToken;

class CompletarCadastroSocial
{
    public function executar(array $dados)
    {
        $usuario = Usuario::findOrFail($dados['usuario_id']);
        $token = PersonalAccessToken::findToken($dados['onboarding_token']);
        $aceitaTermos = filter_var($dados['aceita_termos'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $aceitaNotificacoes = filter_var($dados['aceita_notificacoes'] ?? true, FILTER_VALIDATE_BOOLEAN);

        if (!$token || (int) $token->tokenable_id !== (int) $usuario->id || $token->name !== 'social_onboarding') {
            throw new \Exception('Token de onboarding invalido.', 422);
        }

        if ($usuario->cpf) {
            throw new \Exception('O cadastro deste usuario ja esta completo.', 422);
        }

        $usuario->update([
            'cpf' => $dados['cpf'],
            'data_nascimento' => $dados['data_nascimento'],
            'aceita_termos' => $aceitaTermos,
            'aceita_notificacoes' => $aceitaNotificacoes,
        ]);

        app(GerarCodigoVerificacao::class)->executar($usuario);
        $token->delete();

        return [
            'requer_verificacao' => true,
            'usuario_id' => $usuario->id,
            'email' => $usuario->email,
        ];
    }
}
