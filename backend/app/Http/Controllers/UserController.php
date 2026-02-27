<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Servicos\Usuarios\MostrarUsuario;
use App\Servicos\Usuarios\AtualizarUsuario;
use App\Servicos\Usuarios\RemoverAvatarUsuario;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function mostrar(Request $request, MostrarUsuario $servico)
    {
        return $servico->executar($request);
    }

    public function atualizar(UpdateUserRequest $request, AtualizarUsuario $servico)
    {
        return $servico->executar($request->validated(), $request->file('avatar'));
    }

    public function removerAvatar(Request $request)
    {
        $usuario = $request->user();

        $usuario->avatar = null;
        $usuario->save();

        return response()->json([
            'message' => 'Avatar removido com sucesso',
            'usuario' => $usuario->load('plano.recursos')
        ]);
    }
}
