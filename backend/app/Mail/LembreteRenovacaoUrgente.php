<?php

namespace App\Mail;

use App\Models\Assinatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LembreteRenovacaoUrgente extends Mailable
{
    use Queueable, SerializesModels;

    public $assinatura;
    public $usuario;
    public $plano;
    public $horasRestantes;

    public function __construct(Assinatura $assinatura)
    {
        $this->assinatura = $assinatura;
        $this->usuario = $assinatura->usuario;
        $this->plano = $assinatura->plano;
        $this->horasRestantes = now()->diffInHours($assinatura->termina_em);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 URGENTE: Sua assinatura do Finalyze expira amanhã!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lembrete-renovacao-urgente',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
