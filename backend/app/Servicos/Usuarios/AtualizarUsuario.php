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
            if (!$avatarFile->isValid()) {
                throw new \Exception('O arquivo de avatar é inválido ou excedeu o limite do servidor.');
            }

            if ($usuario->avatar) {
                Storage::disk('public')->delete($usuario->avatar);
            }
            $path = $avatarFile->store('avatars', 'public');

            if (!$path) {
                throw new \Exception('Não foi possível salvar o arquivo de avatar.');
            }

            $usuario->avatar = $path;
        }

        // Remove avatar from dados to prevent it being overwritten by null if handled separately
        unset($dados['avatar']);

        $usuario->fill($dados);
        $usuario->save();

        return $usuario->load('plano.recursos');
    }
}
