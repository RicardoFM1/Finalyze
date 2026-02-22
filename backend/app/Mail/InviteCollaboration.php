<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteCollaboration extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Usuario $owner) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Collaboration Invite: Finalyze Finance'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invite_collaboration',
        );
    }
}
