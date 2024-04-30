<?php

namespace App\Mail;

use App\Exceptions\NoSummaryException;
use App\Models\IcsEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IcsValidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public IcsEvent $icsEvent)
    {
    }

    /**
     * @throws NoSummaryException
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->icsEvent->getSummary(),

        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.ics_success', with: ['ics' => $this->icsEvent]
        );
    }

    /**
     * @throws NoSummaryException
     */
    public function attachments(): array
    {
        return [Attachment::fromData(function () {
            return $this->icsEvent->ics;
        }, $this->icsEvent->getSummary() . '.ics')];
    }
}
