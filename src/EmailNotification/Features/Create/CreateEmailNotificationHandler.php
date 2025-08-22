<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Create;

use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\Shared\Event\EmailNotification\EmailNotificationWasCreated;
use App\Shared\Message\EventBusInterface;
use App\Shared\Message\MessageHandlerInterface;

final readonly class CreateEmailNotificationHandler implements MessageHandlerInterface
{
    public function __construct(
        private EmailNotificationFactoryInterface $createNotification,
        private EmailNotificationRepositoryInterface $notificationRepository,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateEmailNotificationCommand $command): void
    {
        $dto = new CreateEmailNotificationDto($command->email, $command->subject, $command->message);

        $notification = $this->createNotification->create($dto);
        $this->notificationRepository->saveDeferred($notification);

        $this->eventBus->dispatchAfterCurrentBusStamp(new EmailNotificationWasCreated($notification->getUuid()));
    }
}
