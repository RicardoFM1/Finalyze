<?php

namespace App\Mail;

use App\Models\Lembrete;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LembreteNotificacao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lembrete $lembrete) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Reminder: :title', ['title' => $this->lembrete->titulo]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lembrete-anotacao',
        );
    }
}
