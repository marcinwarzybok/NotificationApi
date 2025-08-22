<?php

declare(strict_types=1);

namespace App\Shared\Mailer;

class SendingEmailException extends \RuntimeException
{
    public static function sendingFailed(string $reason): self
    {
        return new self(\sprintf('Sending failed: %s', $reason));
    }
}
