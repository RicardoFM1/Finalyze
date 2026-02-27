<?php

namespace App\Console\Commands;

use App\Models\Lembrete;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarLembretesPessoais extends Command
{
    protected $signature = 'app:enviar-lembretes-pessoais';
    protected $description = 'Envia lembretes por e-mail para lembretes com prazo para hoje';

    public function handle()
    {
        $this->info('Verificando lembretes para envio...');

        $hoje = now()->format('Y-m-d');

        $lembretes = Lembrete::where('status', 'andamento')
            ->whereDate('prazo', '<=', $hoje)
            ->whereNull('email_notified_at')
            ->where('notificacao_email', 'true')
            ->with('usuario')
            ->get();

        if ($lembretes->isEmpty()) {
            $this->info('Nenhum lembrete pendente para envio por e-mail.');
            return 0;
        }

        $contagem = 0;
        foreach ($lembretes as $lembrete) {
            try {
                if ($lembrete->usuario && $lembrete->usuario->email) {
                    $userLocale = $lembrete->usuario->idioma ?? config('app.locale');
                    Log::info("Enviando lembrete

                    Mail::to($lembrete->usuario->email)
                        ->locale($userLocale)
                        ->send(new \App\Mail\LembreteNotificacao($lembrete));

                    $lembrete->update(['email_notified_at' => now()]);
                    $contagem++;
                } else {
                    Log::warning("Lembrete
                }
            } catch (\Exception $e) {
                $this->error("Erro ao enviar para {$lembrete->usuario->email}: {$e->getMessage()}");
                Log::error("Erro no comando EnviarLembretesPessoais: {$e->getMessage()}", [
                    'lembrete_id' => $lembrete->id,
                    'user_email' => $lembrete->usuario->email ?? 'N/A'
                ]);
            }
        }

        $this->info("{$contagem} lembretes enviados com sucesso.");
        return 0;
    }
}
