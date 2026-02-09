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
            $dados['avatar'] = $path;
        }

        $usuario->update($dados);

        return $usuario->load('plano');
    }
}
