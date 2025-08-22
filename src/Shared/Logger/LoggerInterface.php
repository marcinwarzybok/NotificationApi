<?php

declare(strict_types=1);

namespace App\Shared\Logger;

interface LoggerInterface
{
    public function error(string $message): void;
}
