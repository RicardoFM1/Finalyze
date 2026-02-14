<?php

namespace App\Console\Commands;

use App\Models\Assinatura;
use App\Models\LembreteEnviado;
use App\Mail\LembreteRenovacao;
use App\Mail\LembreteRenovacaoUrgente;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarLembretesRenovacao extends Command
{
    protected $signature = 'app:enviar-lembretes-renovacao';

    protected $description = 'Envia lembretes de renovação para assinaturas próximas do vencimento';

    public function handle()
    {
        $this->info('Iniciando envio de lembretes de renovação...');

        $totalEnviados = 0;

        $totalEnviados += $this->enviarLembretes3Dias();

        $totalEnviados += $this->enviarLembretesUrgentes();

        $this->info("\n✓ Processo concluído: {$totalEnviados} lembrete(s) enviado(s).");
        Log::info("Lembretes de renovação enviados: {$totalEnviados} total.");

        return 0;
    }

    private function enviarLembretes3Dias()
    {
        $this->line("\n📧 Verificando assinaturas que expiram em 3 dias...");

        $dataLimite = now()->addDays(3)->endOfDay();
        $dataInicio = now()->addDays(3)->startOfDay();

        $assinaturas = Assinatura::where('status', 'active')
            ->whereBetween('termina_em', [$dataInicio, $dataLimite])
            ->whereDoesntHave('lembretesEnviados', function ($query) {
                $query->where('tipo', '3_dias');
            })
            ->with(['usuario', 'plano'])
            ->get();

        if ($assinaturas->isEmpty()) {
            $this->line('  → Nenhum lembrete de 3 dias para enviar.');
            return 0;
        }

        $count = 0;
        foreach ($assinaturas as $assinatura) {
            try {
                Mail::to($assinatura->usuario->email)
                    ->send(new LembreteRenovacao($assinatura));

                LembreteEnviado::create([
                    'assinatura_id' => $assinatura->id,
                    'tipo' => '3_dias',
                    'enviado_em' => now()
                ]);

                $count++;
                $this->line("  ✓ Lembrete enviado para {$assinatura->usuario->email}");
                Log::info("Lembrete 3 dias enviado: Assinatura #{$assinatura->id}, Usuário: {$assinatura->usuario->email}");
            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao enviar para {$assinatura->usuario->email}");
                Log::error("Erro ao enviar lembrete 3 dias: Assinatura #{$assinatura->id}, Erro: {$e->getMessage()}");
            }
        }

        $this->info("  → {$count} lembrete(s) de 3 dias enviado(s).");
        return $count;
    }

    private function enviarLembretesUrgentes()
    {
        $this->line("\n🚨 Verificando assinaturas que expiram em 1 dia...");

        $dataLimite = now()->addDay()->endOfDay();
        $dataInicio = now()->addDay()->startOfDay();

        $assinaturas = Assinatura::where('status', 'active')
            ->whereBetween('termina_em', [$dataInicio, $dataLimite])
            ->whereDoesntHave('lembretesEnviados', function ($query) {
                $query->where('tipo', '1_dia');
            })
            ->with(['usuario', 'plano'])
            ->get();

        if ($assinaturas->isEmpty()) {
            $this->line('  → Nenhum lembrete urgente para enviar.');
            return 0;
        }

        $count = 0;
        foreach ($assinaturas as $assinatura) {
            try {
                Mail::to($assinatura->usuario->email)
                    ->send(new LembreteRenovacaoUrgente($assinatura));

                LembreteEnviado::create([
                    'assinatura_id' => $assinatura->id,
                    'tipo' => '1_dia',
                    'enviado_em' => now()
                ]);

                $count++;
                $this->line("  ✓ Lembrete URGENTE enviado para {$assinatura->usuario->email}");
                Log::info("Lembrete urgente enviado: Assinatura #{$assinatura->id}, Usuário: {$assinatura->usuario->email}");
            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao enviar para {$assinatura->usuario->email}");
                Log::error("Erro ao enviar lembrete urgente: Assinatura #{$assinatura->id}, Erro: {$e->getMessage()}");
            }
        }

        $this->info("  → {$count} lembrete(s) urgente(s) enviado(s).");
        return $count;
    }
}
