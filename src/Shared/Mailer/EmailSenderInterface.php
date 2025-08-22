<?php

declare(strict_types=1);

namespace App\Shared\Mailer;

interface EmailSenderInterface
{
    /** @throws SendingEmailException */
    public function send(Email $email): void;
}
