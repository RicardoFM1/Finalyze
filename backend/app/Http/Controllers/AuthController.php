<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'cpf' => $request->cpf,
        ]);

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario->load('plano')
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credenciais = [
            'email' => $request->email,
            'password' => $request->senha
        ];

        if (!Auth::attempt($credenciais)) {
            return response()->json([
                'message' => 'Credenciais inválidas. Verifique seu e-mail e senha.'
            ], 401);
        }

        $usuario = Usuario::where('email', $request['email'])->firstOrFail();

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $usuario->load('plano')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }
}
