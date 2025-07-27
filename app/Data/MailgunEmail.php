<?php

namespace App\Data;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class MailgunEmail extends Data
{
    public function __construct(
        public string $recipient,
        public string $sender,
        #[MapInputName('Sender')]
        public ?string $from,
        public ?string $subject,
        public ?string $body,
        #[MapInputName('body-html')]
        public ?string $bodyHtml,
        #[MapInputName('stripped-text')]
        public ?string $strippedText,
        #[MapInputName('stripped-html')]
        public ?string $strippedHtml,
        #[MapInputName('stripped-signature')]
        public ?string $strippedSignature,
        #[MapInputName('attachment-count')]
        public ?int $attachmentCount,
        public int $timestamp,
        public string $token,
        public string $signature,
        #[MapInputName('Message-Id')]
        public string $messageId,
        #[MapInputName('message-headers')]
        public ?string $messageHeaders,
        public ?array $parsedMessageHeaders,
        #[MapInputName('content-id-map')]
        public ?string $contentIdMap,
        #[MapInputName('attachment-1')]
        public ?UploadedFile $attachment1,
        #[MapInputName('attachment-2')]
        public ?UploadedFile $attachment2,
        #[MapInputName('attachment-3')]
        public ?UploadedFile $attachment3,
        #[MapInputName('attachment-4')]
        public ?UploadedFile $attachment4,
        #[MapInputName('attachment-5')]
        public ?UploadedFile $attachment5,
    ) {
        try {
            $this->parsedMessageHeaders ??= json_decode($this->messageHeaders, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            Log::error($e->getMessage());
        }
    }
}
