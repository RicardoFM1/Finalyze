<?php

namespace App\Console\Commands;

use App\Models\Assinatura;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerificarAssinaturasExpiradas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verificar-assinaturas-expiradas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e expira assinaturas que ultrapassaram a data de término';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando assinaturas expiradas...');

        // Encontrar assinaturas ativas que já expiraram
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
                // Atualizar status da assinatura
                $assinatura->update(['status' => 'expired']);

                // Remover plano do usuário
                Usuario::where('id', $assinatura->user_id)
                    ->update(['plano_id' => null]);

                $count++;

                Log::info("Assinatura {$assinatura->id} expirada. Usuário {$assinatura->user_id} teve plano removido.");
                $this->line("✓ Assinatura #{$assinatura->id} expirada (Usuário: {$assinatura->user_id})");
            } catch (\Exception $e) {
                Log::error("Erro ao expirar assinatura {$assinatura->id}: {$e->getMessage()}");
                $this->error("✗ Erro ao processar assinatura #{$assinatura->id}");
            }
        }

        $this->info("\n{$count} assinatura(s) expirada(s) com sucesso.");
        Log::info("Processo de expiração concluído: {$count} assinaturas processadas.");

        return 0;
    }
}
