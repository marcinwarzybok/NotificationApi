<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Create\Entrypoint\Http;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateEmailNotificationRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public ?string $subject,

        #[Assert\NotBlank]
        public ?string $message,
    ) {
    }
}
