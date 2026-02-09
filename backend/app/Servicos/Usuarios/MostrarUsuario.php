<?php

namespace App\Servicos\Usuarios;

use Illuminate\Http\Request;

class MostrarUsuario
{
    public function executar(Request $request)
    {
        return $request->user()->load('plano.recursos');
    }
}
