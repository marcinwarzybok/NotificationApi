<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Create;

final readonly class CreateEmailNotificationCommand
{
    public function __construct(
        public string $email,
        public string $subject,
        public string $message,
    ) {
    }
}
