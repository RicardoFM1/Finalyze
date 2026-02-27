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
                throw new \Exception('Arquivo de avatar inválido.');
            }

            if ($usuario->avatar) {

                if (strpos($usuario->avatar, 'data:') !== 0) {
                    Storage::delete($usuario->avatar);
                }
                $usuario->avatar = null;
            }

            try {
                
                $imageData = file_get_contents($avatarFile->getRealPath());
                $base64 = base64_encode($imageData);
                $mimeType = $avatarFile->getMimeType();

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

            if (isset($dados['avatar']) && (is_null($dados['avatar']) || $dados['avatar'] === '')) {
                if ($usuario->avatar) {
                    
                    if (strpos($usuario->avatar, 'data:') !== 0) {
                        Storage::delete($usuario->avatar);
                    }
                    $usuario->avatar = null;
                    \Log::info('Avatar removed by user request.');
                }
            }
            \Log::info('No new avatar file received.');
        }

        $usuario->fill($dados);
        $usuario->save();

        \Log::info('User saved', ['avatar' => $usuario->avatar]);

        return $usuario->load('plano.recursos');
    }
}
