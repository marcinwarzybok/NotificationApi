<?php

declare(strict_types=1);

namespace App\EmailNotification\Shared\Exception;

use App\Shared\Model\EntityNotFoundException;

final class EmailNotificationNotFoundException extends EntityNotFoundException
{
    public static function byId(string $id): self
    {
        return new self(\sprintf('Email Notification not found by id `%s`', $id));
    }

    public static function byUuid(string $uuid): self
    {
        return new self(\sprintf('Email Notification not found by uuid `%s`', $uuid));
    }
}
