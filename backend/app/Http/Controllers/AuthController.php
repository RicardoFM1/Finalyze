<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Servicos\Autenticacao\RegistrarUsuario;
use App\Servicos\Autenticacao\LoginUsuario;
use App\Servicos\Autenticacao\LogoutUsuario;
use App\Servicos\Autenticacao\VerificarCodigo;
use App\Servicos\Autenticacao\ReenviarCodigo;
use App\Servicos\Autenticacao\GoogleAuthCallback;
use App\Servicos\Autenticacao\CompletarCadastroSocial;
use App\Servicos\Autenticacao\EnviarCodigoResetSenha;
use App\Servicos\Autenticacao\RedefinirSenha;
use Laravel\Socialite\Facades\Socialite;
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

    public function verificarCodigo(Request $request, VerificarCodigo $servico)
    {
        $dados = $request->validate([
            'email' => 'required|email',
            'codigo' => 'required|string|size:6'
        ]);

        try {
            return response()->json($servico->executar($dados['email'], $dados['codigo']));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function reenviarCodigo(Request $request, ReenviarCodigo $servico)
    {
        $dados = $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $servico->executar($dados['email']);
            return response()->json(['message' => 'Código reenviado com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function logout(Request $request, LogoutUsuario $servico)
    {
        $servico->executar($request);
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback(GoogleAuthCallback $servico)
    {
        try {
            $resultado = $servico->executar();
            $query = http_build_query($resultado);
            return redirect(config('app.frontend_url') . '/auth/callback?' . $query);
        } catch (\Exception $e) {
            return redirect(config('app.frontend_url') . '/login?error=' . urlencode($e->getMessage()));
        }
    }

    public function completarCadastroSocial(Request $request, CompletarCadastroSocial $servico)
    {
        $dados = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'cpf' => 'required|string', // A validação real pode ser feita via regex ou custom rule
            'data_nascimento' => 'required|date',
            'codigo' => 'required|string|size:6',
            'aceita_termos' => 'required|accepted',
            'aceita_notificacoes' => 'boolean'
        ]);

        try {
            return response()->json($servico->executar($dados));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function forgotPassword(Request $request, EnviarCodigoResetSenha $servico)
    {
        $request->validate(['email' => 'required|email']);
        $servico->executar($request->email);
        return response()->json(['message' => 'Se o e-mail estiver cadastrado, um código de recuperação será enviado.']);
    }

    public function resetPassword(Request $request, RedefinirSenha $servico)
    {
        $dados = $request->validate([
            'email' => 'required|email',
            'codigo' => 'required|string|size:6',
            'senha' => 'required|min:6|confirmed'
        ]);

        try {
            $servico->executar($dados);
            return response()->json(['message' => 'Senha redefinida com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
