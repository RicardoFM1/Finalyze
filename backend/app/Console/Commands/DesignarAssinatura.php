<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Plano;
use App\Servicos\Assinaturas\AtivarAssinaturaManual;

class DesignarAssinatura extends Command
{
    protected $signature = 'app:designar-assinatura {user_id} {plano_id} {periodo_id?}';
    protected $description = 'Designa manualmente um plano para um usuário';

    public function handle(AtivarAssinaturaManual $servico)
    {
        $userId = $this->argument('user_id');
        $planoId = $this->argument('plano_id');
        $periodoId = $this->argument('periodo_id');

        try {
            $assinatura = $servico->executar((int)$userId, (int)$planoId, $periodoId ? (int)$periodoId : null);
            $this->info("Assinatura ativada com sucesso para o usuário {$userId} no plano {$planoId}!");
            $this->line("Data de término: " . $assinatura->termina_em->format('d/m/Y H:i'));
        } catch (\Exception $e) {
            $this->error("Erro ao ativar assinatura: " . $e->getMessage());
        }
    }
}
