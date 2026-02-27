<?php

namespace App\Console\Commands;

use App\Models\Meta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacoesMetas extends Command
{
    protected $signature = 'app:enviar-notificacoes-metas';
    protected $description = 'Envia notificações por e-mail para metas com prazo próximo ou vencendo';

    public function handle()
    {
        $this->info('Verificando metas para envio de notificações...');

        $hoje = now()->format('Y-m-d');

        // Selecionamos metas em andamento que venceram ou vencem hoje, e que ainda não foram notificadas
        $metas = Meta::where('status', 'andamento')
            ->whereDate('prazo', '<=', $hoje)
            ->whereNull('email_notified_at')
            ->where('notificacao_email', 'true')
            ->with('usuario')
            ->get();

        if ($metas->isEmpty()) {
            $this->info('Nenhuma meta pendente para notificação por e-mail.');
            return 0;
        }

        $contagem = 0;
        foreach ($metas as $meta) {
            try {
                if ($meta->usuario && $meta->usuario->email) {
                    Mail::to($meta->usuario->email)->send(new \App\Mail\MetaNotificacao($meta));
                    $meta->update(['email_notified_at' => now()]);
                    $contagem++;
                }
            } catch (\Exception $e) {
                $this->error("Erro ao enviar para {$meta->usuario->email}: {$e->getMessage()}");
                Log::error("Erro no comando EnviarNotificacoesMetas: {$e->getMessage()}");
            }
        }

        $this->info("{$contagem} notificações de metas enviadas com sucesso.");
        return 0;
    }
}
