<?php

declare(strict_types=1);

namespace App\Shared\Uuid;

interface UuidProviderInterface
{
    public function generate(): string;
}
