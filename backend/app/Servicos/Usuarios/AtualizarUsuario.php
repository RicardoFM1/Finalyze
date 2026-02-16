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
            \Log::info('Avatar file received', [
                'name' => $avatarFile->getClientOriginalName(),
                'size' => $avatarFile->getSize(),
                'mime' => $avatarFile->getMimeType()
            ]);

            if (!$avatarFile->isValid()) {
                \Log::error('Avatar file is not valid', ['error' => $avatarFile->getErrorMessage()]);
                throw new \Exception('O arquivo de avatar é inválido ou excedeu o limite do servidor.');
            }

            if ($usuario->avatar) {
                Storage::disk('public')->delete($usuario->avatar);
            }
            $path = $avatarFile->store('avatars', 'public');

            if (!$path) {
                \Log::error('Failed to store avatar file');
                throw new \Exception('Não foi possível salvar o arquivo de avatar.');
            }

            \Log::info('Avatar stored successfully', ['path' => $path]);
            $usuario->avatar = $path;
        } else {
            \Log::info('No avatar file received');
        }

        // Remove avatar from dados to prevent it being overwritten by null if handled separately
        unset($dados['avatar']);

        $usuario->fill($dados);
        $usuario->save();

        \Log::info('User saved', ['avatar' => $usuario->avatar]);

        return $usuario->load('plano.recursos');
    }
}
