<?php

declare(strict_types=1);

namespace App\Shared\Message;

use Symfony\Component\Messenger\Exception\RuntimeException;

final class MessageException extends RuntimeException
{
}
