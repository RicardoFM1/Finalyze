<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoResetSenha extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $codigo;

    public function __construct(Usuario $usuario, string $codigo)
    {
        $this->usuario = $usuario;
        $this->codigo = $codigo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Reset Your Password - Finalyze Finance'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-senha',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
