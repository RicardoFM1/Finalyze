<?php

namespace App\Mail;

use App\Models\ConviteEnviado;
use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConviteCompartilhamento extends Mailable
{
    use Queueable, SerializesModels;

    public Usuario $usuarioOrigem;
    public ConviteEnviado $convite;
    public string $linkConvite;

    public function __construct(Usuario $usuarioOrigem, ConviteEnviado $convite, string $linkConvite)
    {
        $this->usuarioOrigem = $usuarioOrigem;
        $this->convite = $convite;
        $this->linkConvite = $linkConvite;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Convite de compartilhamento - Finalyze',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.convite-compartilhamento',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

