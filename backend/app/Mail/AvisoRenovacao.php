<?php

namespace App\Mail;

use App\Models\Assinatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AvisoRenovacao extends Mailable
{
    use Queueable, SerializesModels;

    public $assinatura;
    public $usuario;
    public $plano;
    public $diasRestantes;

    /**
     * Create a new message instance.
     */
    public function __construct(Assinatura $assinatura)
    {
        $this->assinatura = $assinatura;
        $this->usuario = $assinatura->usuario;
        $this->plano = $assinatura->plano;
        $this->diasRestantes = now()->diffInDays($assinatura->termina_em);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Sua assinatura do Finalyze expira em breve',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lembrete-renovacao',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
