<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Send;

use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\Shared\Event\EmailNotification\EmailNotificationSendingFailed;
use App\Shared\Event\EmailNotification\EmailNotificationWasSent;
use App\Shared\Mailer\EmailSenderInterface;
use App\Shared\Mailer\SendingEmailException;
use App\Shared\Message\EventBusInterface;
use App\Shared\Message\MessageException;
use App\Shared\Message\MessageHandlerInterface;

final readonly class SendNotificationHandler implements MessageHandlerInterface
{
    public function __construct(
        private EmailNotificationRepositoryInterface $notificationRepository,
        private EmailSenderInterface $emailSender,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(SendNotificationCommand $command): void
    {
        $emailNotification = $this->notificationRepository->getByUuid($command->uuid);
        if ($emailNotification->isSent()) {
            throw CannotResendEmailNotificationException::byUuid($command->uuid);
        }

        try {
            $email = EmailConverter::fromEmailNotification($emailNotification);
            $this->emailSender->send($email);
            $emailNotification->markAsSent();
        } catch (SendingEmailException $e) {
            try {
                $this->eventBus->dispatch(
                    new EmailNotificationSendingFailed(
                        $emailNotification->getUuid(),
                        $e->getMessage()
                    )
                );
            } catch (MessageException) {
            }

            throw $e;
        }

        try {
            $this->eventBus->dispatchAfterCurrentBusStamp(new EmailNotificationWasSent($emailNotification->getUuid()));
        } catch (MessageException) {
        }
    }
}
