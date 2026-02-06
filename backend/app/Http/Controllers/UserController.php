<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function mostrar(Request $request)
    {
        return $request->user()->load('plano.recursos');
    }

    public function atualizar(UpdateUserRequest $request)
    {
        $usuario = Auth::user();

        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($usuario->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($usuario->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $usuario->update($validated);

        return $usuario->load('plano');
    }

    public function removerAvatar()
    {
        $usuario = Auth::user();

        if ($usuario->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($usuario->avatar);
            $usuario->update(['avatar' => null]);
        }

        return response()->json([
            'message' => 'Avatar removido com sucesso.',
            'usuario' => $usuario->load('plano')
        ]);
    }
}
