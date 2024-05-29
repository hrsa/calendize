<?php

namespace App\Mail;

use BeyondCode\Mailbox\InboundEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use ZBateson\MailMimeParser\Message\MessagePart;

class ForwardEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public InboundEmail $inboundEmail)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FROM: ' . $this->inboundEmail->fromName() . ' (' . $this->inboundEmail->from() . ') - ' . $this->inboundEmail->subject(),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->inboundEmail->html()
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        /** @var MessagePart $attachment */
        foreach ($this->inboundEmail->attachments() as $attachment) {
            if ($attachment->getFilename()) {
                $attachments[] = Attachment::fromData(fn () => $attachment->getContent(), $attachment->getFilename())
                    ->withMime($attachment->getContentType());
            }
        }

        return $attachments;
    }
}
