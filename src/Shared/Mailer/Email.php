<?php

declare(strict_types=1);

namespace App\Shared\Mailer;

final readonly class Email
{
    /**
     * @param string[]     $recipients
     * @param Attachment[] $attachments
     */
    public function __construct(
        public array $recipients,
        public string $subject,
        public string $body,
        public array $attachments = [],
        public ?string $replyTo = null,
    ) {
    }
}
