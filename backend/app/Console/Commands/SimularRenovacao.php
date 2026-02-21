<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use App\Models\Assinatura;
use App\Models\Plano;
use App\Servicos\Checkout\AtivarPlanoUsuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SimularRenovacao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:simular-renovacao {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula uma renovação automática do Mercado Pago para um usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario) {
            $this->error("Usuário com e-mail {$email} não encontrado.");
            return;
        }

        $assinaturaAtiva = $usuario->assinaturaAtiva();

        if (!$assinaturaAtiva) {
            $this->error("O usuário {$email} não possui uma assinatura ativa para renovar.");
            return;
        }

        $this->info("Simulando renovação para o usuário: {$usuario->nome}");
        $this->info("Plano atual: {$assinaturaAtiva->plano->nome}");
        $this->info("Vencimento atual: " . $assinaturaAtiva->termina_em->format('d/m/Y H:i:s'));

        // Busca o valor do plano/período (pivot)
        $valorEmCentavos = \DB::table('plano_periodo')
            ->where('plano_id', $assinaturaAtiva->plano_id)
            ->where('periodo_id', $assinaturaAtiva->periodo_id)
            ->value('valor_centavos') ?? 0;

        // Mock do objeto de pagamento que o Mercado Pago enviaria no Webhook
        $mockPayment = (object)[
            'id' => 'SIM-RENOV-' . time(),
            'transaction_amount' => (float)($valorEmCentavos / 100),
            'payment_method_id' => 'credit_card',
            'status' => 'approved',
            'status_detail' => 'accredited',
            'external_reference' => (string)$assinaturaAtiva->id, // Referência à assinatura ID
            'metadata' => [
                'user_id' => $usuario->id,
                'plano_id' => $assinaturaAtiva->plano_id,
                'periodo_id' => $assinaturaAtiva->periodo_id,
                'assinatura_id' => $assinaturaAtiva->id,
                'quantidade_dias' => $assinaturaAtiva->periodo->quantidade_dias ?? 30,
                'creditos_prorrata' => 0 // Renovação não tem prorrata
            ]
        ];

        try {
            $ativarServico = new AtivarPlanoUsuario();
            $ativarServico->executar($mockPayment);

            // Refetch para mostrar o novo vencimento
            $assinaturaAtiva->refresh();

            $this->success("Renovação processada com sucesso!");
            $this->info("Novo vencimento: " . $assinaturaAtiva->termina_em->format('d/m/Y H:i:s'));
        } catch (\Exception $e) {
            $this->error("Erro ao processar renovação: " . $e->getMessage());
            Log::error("Simulação de renovação falhou: " . $e->getMessage());
        }
    }

    private function success($message)
    {
        $this->output->writeln("<info>{$message}</info>");
    }
}
