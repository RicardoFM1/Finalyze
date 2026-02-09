<?php

namespace App\Servicos\Autenticacao;

use Illuminate\Http\Request;

class LogoutUsuario
{
    public function executar(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }
}
