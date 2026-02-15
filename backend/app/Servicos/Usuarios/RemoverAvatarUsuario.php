<?php

namespace App\Servicos\Usuarios;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RemoverAvatarUsuario
{
    public function executar()
    {
        $usuario = Auth::user();

        if ($usuario->avatar) {
            Storage::disk('public')->delete($usuario->avatar);
            $usuario->update(['avatar' => null]);
        }

        return $usuario->load('plano.recursos');
    }
}
