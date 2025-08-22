<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Send;

final readonly class SendNotificationCommand
{
    public function __construct(public string $uuid)
    {
    }
}
