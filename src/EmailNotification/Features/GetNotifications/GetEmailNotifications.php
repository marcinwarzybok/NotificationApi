<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\GetNotifications;

use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\EmailNotification\Shared\Model\EmailNotification;

final readonly class GetEmailNotifications implements GetEmailNotificationsInterface
{
    public function __construct(private EmailNotificationRepositoryInterface $emailNotificationRepository)
    {
    }

    public function execute(): array
    {
        $emailNotifications = $this->emailNotificationRepository->findAll();

        return array_values(array_map(fn (EmailNotification $notification): array => [
            'uuid' => $notification->getUuid(),
            'recipients' => $notification->getRecipients(),
            'subject' => $notification->getSubject(),
            'body' => $notification->getMessage(),
            'status' => $notification->getStatus()->value,
            'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $emailNotifications));
    }
}
