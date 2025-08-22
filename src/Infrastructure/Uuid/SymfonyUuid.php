<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use App\Shared\Uuid\UuidProviderInterface;
use Symfony\Component\Uid\Uuid;

final readonly class SymfonyUuid implements UuidProviderInterface
{
    public function generate(): string
    {
        return Uuid::v7()->toString();
    }
}
