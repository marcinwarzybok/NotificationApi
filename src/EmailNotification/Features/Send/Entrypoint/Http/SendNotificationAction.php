<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Send\Entrypoint\Http;

use App\EmailNotification\Features\Send\CannotResendEmailNotificationException;
use App\EmailNotification\Features\Send\SendNotificationCommand;
use App\Shared\Message\MessageBusInterface;
use App\Shared\Message\MessageException;
use App\Shared\Model\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final readonly class SendNotificationAction
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(string $uuid): JsonResponse
    {
        try {
            $command = new SendNotificationCommand($uuid);
            $this->messageBus->dispatch($command);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (MessageException $exception) {
            $throwable = $exception->getPrevious();
            if ($throwable instanceof CannotResendEmailNotificationException) {
                return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            if ($throwable instanceof EntityNotFoundException) {
                return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Email Notification could not be processed',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
