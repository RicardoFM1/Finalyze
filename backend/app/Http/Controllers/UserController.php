<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return $request->user()->load('plano.recursos');
    }

    public function update(UpdateUserRequest $request)
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
}
