<?php

namespace App\Console\Commands;

use App\Models\Assinatura;
use App\Models\AvisoEnviado;
use App\Mail\AvisoRenovacao;
use App\Mail\AvisoRenovacaoUrgente;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarAvisosRenovacao extends Command
{
    protected $signature = 'app:enviar-avisos-renovacao';

    protected $description = 'Envia avisos de renovação para assinaturas próximas do vencimento';

    public function handle()
    {
        $this->info('Iniciando envio de avisos de renovação...');

        $totalEnviados = 0;

        $totalEnviados += $this->enviarAvisos3Dias();

        $totalEnviados += $this->enviarAvisosUrgentes();

        $this->info("\n✓ Processo concluído: {$totalEnviados} aviso(s) enviado(s).");
        Log::info("Avisos de renovação enviados: {$totalEnviados} total.");

        return 0;
    }

    private function enviarAvisos3Dias()
    {
        $this->line("\n📧 Verificando assinaturas que expiram em 3 dias...");

        $dataLimite = now()->addDays(3)->endOfDay();
        $dataInicio = now()->addDays(3)->startOfDay();

        $assinaturas = Assinatura::where('status', 'active')
            ->whereBetween('termina_em', [$dataInicio, $dataLimite])
            ->whereDoesntHave('avisosEnviados', function ($query) {
                $query->where('tipo', '3_dias');
            })
            ->with(['usuario', 'plano'])
            ->get();

        if ($assinaturas->isEmpty()) {
            $this->line('  → Nenhum aviso de 3 dias para enviar.');
            return 0;
        }

        $count = 0;
        foreach ($assinaturas as $assinatura) {
            try {
                Mail::to($assinatura->usuario->email)
                    ->send(new AvisoRenovacao($assinatura));

                AvisoEnviado::create([
                    'assinatura_id' => $assinatura->id,
                    'tipo' => '3_dias',
                    'enviado_em' => now()
                ]);

                $count++;
                $this->line("  ✓ Aviso enviado para {$assinatura->usuario->email}");
                Log::info("Aviso 3 dias enviado: Assinatura
            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao enviar para {$assinatura->usuario->email}");
                Log::error("Erro ao enviar aviso 3 dias: Assinatura
            }
        }

        $this->info("  → {$count} aviso(s) de 3 dias enviado(s).");
        return $count;
    }

    private function enviarAvisosUrgentes()
    {
        $this->line("\n🚨 Verificando assinaturas que expiram em 1 dia...");

        $dataLimite = now()->addDay()->endOfDay();
        $dataInicio = now()->addDay()->startOfDay();

        $assinaturas = Assinatura::where('status', 'active')
            ->whereBetween('termina_em', [$dataInicio, $dataLimite])
            ->whereDoesntHave('avisosEnviados', function ($query) {
                $query->where('tipo', '1_dia');
            })
            ->with(['usuario', 'plano'])
            ->get();

        if ($assinaturas->isEmpty()) {
            $this->line('  → Nenhum aviso urgente para enviar.');
            return 0;
        }

        $count = 0;
        foreach ($assinaturas as $assinatura) {
            try {
                Mail::to($assinatura->usuario->email)
                    ->send(new AvisoRenovacaoUrgente($assinatura));

                AvisoEnviado::create([
                    'assinatura_id' => $assinatura->id,
                    'tipo' => '1_dia',
                    'enviado_em' => now()
                ]);

                $count++;
                $this->line("  ✓ Aviso URGENTE enviado para {$assinatura->usuario->email}");
                Log::info("Aviso urgente enviado: Assinatura
            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao enviar para {$assinatura->usuario->email}");
                Log::error("Erro ao enviar aviso urgente: Assinatura
            }
        }

        $this->info("  → {$count} aviso(s) urgente(s) enviado(s).");
        return $count;
    }
}
