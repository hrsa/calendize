<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IcsValidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $from)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ics Valid',
        );
    }

    public function content(): Content
    {
        return new Content(
            'icsvalid'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
