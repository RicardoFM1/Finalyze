<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthCallback
{
    public function executar()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            throw new \Exception('Falha na autenticacao com o Google.', 422);
        }

        $usuario = Usuario::where('google_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();

        if (!$usuario) {
            $usuario = Usuario::create([
                'nome' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'senha' => null,
            ]);
        } elseif (!$usuario->google_id) {
            $usuario->update(['google_id' => $googleUser->id]);
        }

        app(GerarCodigoVerificacao::class)->executar($usuario);

        if (!$usuario->cpf || !$usuario->data_nascimento) {
            $onboardingToken = $usuario->createToken('social_onboarding')->plainTextToken;

            return [
                'requer_cadastro_completo' => true,
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'onboarding_token' => $onboardingToken,
            ];
        }

        return [
            'requer_verificacao' => true,
            'email' => $usuario->email,
        ];
    }
}
