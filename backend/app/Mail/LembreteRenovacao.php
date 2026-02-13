<?php

namespace App\Mail;

use App\Models\Assinatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LembreteRenovacao extends Mailable
{
    use Queueable, SerializesModels;

    public $assinatura;
    public $usuario;
    public $plano;
    public $diasRestantes;

    public function __construct(Assinatura $assinatura)
    {
        $this->assinatura = $assinatura;
        $this->usuario = $assinatura->usuario;
        $this->plano = $assinatura->plano;
        $this->diasRestantes = now()->diffInDays($assinatura->termina_em);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Sua assinatura do Finalyze expira em breve',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lembrete-renovacao',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
