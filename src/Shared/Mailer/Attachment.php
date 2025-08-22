<?php

declare(strict_types=1);

namespace App\Shared\Mailer;

final readonly class Attachment
{
    public function __construct(public string $filePath)
    {
    }
}
