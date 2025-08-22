<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Create;

use DaveLiddament\PhpLanguageExtensions\Friend;

final readonly class CreateEmailNotificationDto
{
    #[Friend(CreateEmailNotificationHandler::class)]
    public function __construct(
        public string $email,
        public string $subject,
        public string $message,
    ) {
    }
}
