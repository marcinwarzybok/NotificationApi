<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Create;

use App\EmailNotification\Shared\Model\EmailNotification;
use App\Shared\Uuid\UuidProviderInterface;

final readonly class EmailNotificationFactory implements EmailNotificationFactoryInterface
{
    public function __construct(private UuidProviderInterface $uuidProvider)
    {
    }

    public function create(CreateEmailNotificationDto $dto): EmailNotification
    {
        return new EmailNotification(
            uuid: $this->uuidProvider->generate(),
            message: $dto->message,
            recipients: [$dto->email],
            subject: $dto->subject,
        );
    }
}
