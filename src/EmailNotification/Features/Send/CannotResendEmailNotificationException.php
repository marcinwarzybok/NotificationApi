<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Send;

final class CannotResendEmailNotificationException extends \RuntimeException
{
    public static function byUuid(string $uuid): CannotResendEmailNotificationException
    {
        return new self(\sprintf('Cannot resend email with uuid: %s', $uuid));
    }
}
