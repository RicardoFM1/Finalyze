<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Servicos\Autenticacao\RegistrarUsuario;
use App\Servicos\Autenticacao\LoginUsuario;
use App\Servicos\Autenticacao\LogoutUsuario;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegistrarUsuario $servico)
    {
        $usuario = $servico->executar($request->validated());

        return response()->json([
            'usuario' => $usuario
        ]);
    }

    public function login(LoginRequest $request, LoginUsuario $servico)
    {
        try {
            $dados = $servico->executar($request->validated());
            return response()->json($dados);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 401);
        }
    }

    public function logout(Request $request, LogoutUsuario $servico)
    {
        $servico->executar($request);
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }
}
