<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;

class ReenviarCodigo
{
    public function executar(string $email)
    {
        $usuario = Usuario::where('email', $email)->firstOrFail();

        return app(GerarCodigoVerificacao::class)->executar($usuario);
    }
}
