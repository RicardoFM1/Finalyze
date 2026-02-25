<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use App\Mail\CodigoResetSenha;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EnviarCodigoResetSenha
{
    public function executar(string $email)
    {
        $usuario = Usuario::where('email', $email)->first();

        // Por segurança, se o usuário não existir, não avisamos (evita enumeração de e-mails)
        if (!$usuario) {
            return;
        }

        $codigo = (string) rand(100000, 999999);
        $usuario->update([
            'reset_code' => $codigo,
            'reset_code_expires_at' => now()->addHour()
        ]);

        Mail::to($usuario->email)->send(new CodigoResetSenha($usuario, $codigo));
    }
}
