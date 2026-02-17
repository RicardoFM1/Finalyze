<?php

namespace App\Servicos\Usuarios;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AtualizarUsuario
{
    public function executar(array $dados, $avatarFile = null)
    {
        $usuario = Auth::user();

        // Avatar em base64
        if ($avatarFile) {
            if (!$avatarFile->isValid()) {
                throw new \Exception('Arquivo de avatar inválido.');
            }

            // Remove avatar antigo se existir
            if ($usuario->avatar) {
                // If the old avatar was a file path, delete it.
                // If it was base64, setting to null is sufficient.
                // Assuming previous avatars might have been file paths,
                // we should check if it's a path before attempting to delete.
                if (strpos($usuario->avatar, 'data:') !== 0) { // If it's not a base64 string
                    Storage::delete($usuario->avatar);
                }
                $usuario->avatar = null;
            }

            try {
                // Converte para base64
                $imageData = file_get_contents($avatarFile->getRealPath());
                $base64 = base64_encode($imageData);
                $mimeType = $avatarFile->getMimeType();

                // Formato: data:image/png;base64,iVBORw0KG...
                $dados['avatar'] = "data:{$mimeType};base64,{$base64}";

                \Log::info('Avatar convertido para base64', [
                    'mime_type' => $mimeType,
                    'size_bytes' => strlen($imageData)
                ]);
            } catch (\Exception $e) {
                \Log::error('Exception converting avatar to base64', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw new \Exception('Erro ao processar avatar: ' . $e->getMessage());
            }
        } else {
            // If no new avatar file is provided, and 'avatar' key exists in $dados
            // and its value is explicitly null or empty, it means the user wants to remove the avatar.
            if (isset($dados['avatar']) && (is_null($dados['avatar']) || $dados['avatar'] === '')) {
                if ($usuario->avatar) {
                    // If the old avatar was a file path, delete it.
                    if (strpos($usuario->avatar, 'data:') !== 0) {
                        Storage::delete($usuario->avatar);
                    }
                    $usuario->avatar = null;
                    \Log::info('Avatar removed by user request.');
                }
            }
            \Log::info('No new avatar file received.');
        }

        // Remove avatar from dados to prevent it being overwritten by null if handled separately
        unset($dados['avatar']);

        $usuario->fill($dados);
        $usuario->save();

        \Log::info('User saved', ['avatar' => $usuario->avatar]);

        return $usuario->load('plano.recursos');
    }
}
