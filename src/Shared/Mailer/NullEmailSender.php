<?php

declare(strict_types=1);

namespace App\Shared\Mailer;

final readonly class NullEmailSender implements EmailSenderInterface
{
    public function send(Email $email): void
    {
    }
}
