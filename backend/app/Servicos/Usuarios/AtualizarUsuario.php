<?php

namespace App\Servicos\Usuarios;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AtualizarUsuario
{
    public function executar(array $dados, $avatarFile = null)
    {
        $usuario = Auth::user();

        if ($avatarFile) {
            if ($usuario->avatar) {
                Storage::disk('public')->delete($usuario->avatar);
            }
            $path = $avatarFile->store('avatars', 'public');
            $usuario->avatar = $path;
        }

        // Remove avatar from dados to prevent it being overwritten by null if handled separately
        unset($dados['avatar']);

        $usuario->fill($dados);
        $usuario->save();

        return $usuario->load('plano.recursos');
    }
}
