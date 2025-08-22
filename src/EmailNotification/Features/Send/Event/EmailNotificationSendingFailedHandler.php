<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Send\Event;

use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\Shared\Event\EmailNotification\EmailNotificationSendingFailed;
use App\Shared\Logger\LoggerInterface;
use App\Shared\Message\MessageHandlerInterface;

final readonly class EmailNotificationSendingFailedHandler implements MessageHandlerInterface
{
    public function __construct(
        private EmailNotificationRepositoryInterface $notificationRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(EmailNotificationSendingFailed $event): void
    {
        $emailNotification = $this->notificationRepository->getByUuid($event->uuid);
        $emailNotification->markAsFailed();

        $this->notificationRepository->save($emailNotification);

        $this->logger->error(sprintf('Email notification failed to send for uuid: %s. %s', $event->uuid, $event->errorMessage));
    }
}
