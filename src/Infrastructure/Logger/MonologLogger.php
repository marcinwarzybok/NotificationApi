<?php

declare(strict_types=1);

namespace App\Infrastructure\Logger;

use App\Shared\Logger\LoggerInterface;

final readonly class MonologLogger implements LoggerInterface
{
    public function __construct(private \Psr\Log\LoggerInterface $logger)
    {
    }

    public function error(string $message): void
    {
        $this->logger->error($message);
    }
}
