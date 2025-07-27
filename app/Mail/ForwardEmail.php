<?php

namespace App\Mail;

use App\Data\MailgunEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForwardEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MailgunEmail $inboundEmail) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FROM: ' . $this->inboundEmail->from . ' - ' . $this->inboundEmail->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->inboundEmail->bodyHtml
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        for ($i = 1; $i <= 5; $i++) {
            $attachmentProperty = "attachment{$i}";

            if ($this->inboundEmail->{$attachmentProperty}) {
                $attachments[] = Attachment::fromUploadedFile($this->inboundEmail->{$attachmentProperty});
            }
        }

        return $attachments;
    }
}
