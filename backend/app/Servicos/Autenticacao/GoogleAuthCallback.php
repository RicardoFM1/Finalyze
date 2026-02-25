<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthCallback
{
    public function executar()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            throw new \Exception('Falha na autenticação com o Google.', 422);
        }

        $usuario = Usuario::where('google_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();

        if (!$usuario) {
            $usuario = Usuario::create([
                'nome' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'senha' => null, // Não precisa de senha inicialmente
            ]);
        } else if (!$usuario->google_id) {
            $usuario->update(['google_id' => $googleUser->id]);
        }

        // Sempre enviamos o código de verificação para garantir a posse do e-mail
        app(GerarCodigoVerificacao::class)->executar($usuario);

        // Se faltar dados obrigatórios
        if (!$usuario->cpf || !$usuario->data_nascimento) {
            return [
                'requer_cadastro_completo' => true,
                'usuario_id' => $usuario->id,
                'email' => $usuario->email
            ];
        }

        return [
            'requer_verificacao' => true,
            'email' => $usuario->email
        ];
    }
}
