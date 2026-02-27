<?php

namespace App\Console\Commands;

use App\Models\Assinatura;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerificarAssinaturasExpiradas extends Command
{
    protected $signature = 'app:verificar-assinaturas-expiradas';

    protected $description = 'Verifica e expira assinaturas que ultrapassaram a data de término';

    public function handle()
    {
        $this->info('Verificando assinaturas expiradas...');

        $assinaturasExpiradas = Assinatura::where('status', 'active')
            ->where('termina_em', '<', now())
            ->get();

        if ($assinaturasExpiradas->isEmpty()) {
            $this->info('Nenhuma assinatura expirada encontrada.');
            Log::info('Verificação de assinaturas: Nenhuma expiração detectada.');
            return 0;
        }

        $count = 0;
        foreach ($assinaturasExpiradas as $assinatura) {
            try {
                $assinatura->update(['status' => 'expired']);

                Usuario::where('id', $assinatura->user_id)
                    ->update(['plano_id' => null]);

                $count++;

                Log::info("Assinatura {$assinatura->id} expirada. Usuário {$assinatura->user_id} teve plano removido.");
                $this->line("✓ Assinatura
            } catch (\Exception $e) {
                Log::error("Erro ao expirar assinatura {$assinatura->id}: {$e->getMessage()}");
                $this->error("✗ Erro ao processar assinatura
            }
        }

        $this->info("\n{$count} assinatura(s) expirada(s) com sucesso.");
        Log::info("Processo de expiração concluído: {$count} assinaturas processadas.");

        return 0;
    }
}
