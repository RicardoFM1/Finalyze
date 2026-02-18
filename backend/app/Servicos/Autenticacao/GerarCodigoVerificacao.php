<?php

namespace App\Servicos\Autenticacao;

use App\Models\Usuario;
use App\Mail\CodigoVerificacao;
use Illuminate\Support\Facades\Mail;

class GerarCodigoVerificacao
{
    public function executar(Usuario $usuario)
    {
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $usuario->update([
            'codigo_verificacao' => $codigo,
            'codigo_expira_em' => now()->addMinutes(5)
        ]);

        Mail::to($usuario->email)->send(new CodigoVerificacao($usuario, $codigo));

        return $codigo;
    }
}
