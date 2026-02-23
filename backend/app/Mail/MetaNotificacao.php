<?php

namespace App\Mail;

use App\Models\Meta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MetaNotificacao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Meta $meta) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Goal Reminder: :title', ['title' => $this->meta->titulo]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.meta-notificacao',
        );
    }
}
