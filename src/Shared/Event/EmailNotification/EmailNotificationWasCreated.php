<?php

declare(strict_types=1);

namespace App\Shared\Event\EmailNotification;

use App\Shared\Event\EventInterface;

final readonly class EmailNotificationWasCreated implements EventInterface
{
    public function __construct(public string $uuid)
    {
    }
}
