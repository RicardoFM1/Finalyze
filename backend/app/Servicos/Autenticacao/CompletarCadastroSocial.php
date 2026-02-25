<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;

class CompletarCadastroSocial
{
    public function executar(array $dados)
    {
        $usuario = Usuario::findOrFail($dados['usuario_id']);
        $aceitaTermos = filter_var($dados['aceita_termos'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $aceitaNotificacoes = filter_var($dados['aceita_notificacoes'] ?? true, FILTER_VALIDATE_BOOLEAN);

        if ($usuario->cpf) {
            throw new \Exception('O cadastro deste usuário já está completo.', 422);
        }

        // Validação do código
        if ($usuario->codigo_verificacao !== $dados['codigo']) {
            throw new \Exception('Código de verificação inválido.', 422);
        }

        if ($usuario->codigo_expira_em < now()) {
            throw new \Exception('Código de verificação expirado.', 422);
        }

        // Se o código for válido, completa o cadastro e marca como verificado
        $usuario->update([
            'cpf' => $dados['cpf'],
            'data_nascimento' => $dados['data_nascimento'],
            'aceita_termos' => $aceitaTermos ? 'true' : 'false',
            'aceita_notificacoes' => $aceitaNotificacoes ? 'true' : 'false',
            'email_verified_at' => now(),
            'codigo_verificacao' => null,
            'codigo_expira_em' => null
        ]);

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return [
            'requer_verificacao' => false,
            'access_token' => $token,
            'usuario' => $usuario->load('plano.recursos')
        ];
    }
}
