<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Send;

use App\EmailNotification\Shared\Model\EmailNotification;
use App\Shared\Mailer\Email;
use DaveLiddament\PhpLanguageExtensions\Friend;

final readonly class EmailConverter
{
    #[Friend(SendNotificationHandler::class)]
    public static function fromEmailNotification(EmailNotification $notification): Email
    {
        return new Email(
            recipients: $notification->getRecipients(),
            subject: $notification->getSubject(),
            body: $notification->getMessage(),
            attachments: [],
            replyTo: null
        );
    }
}
