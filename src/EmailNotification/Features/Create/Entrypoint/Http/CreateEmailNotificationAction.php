<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Create\Entrypoint\Http;

use App\EmailNotification\Features\Create\CreateEmailNotificationCommand;
use App\Shared\Message\MessageBusInterface;
use App\Shared\Message\MessageException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Webmozart\Assert\Assert;

#[AsController]
final readonly class CreateEmailNotificationAction
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(#[MapRequestPayload] CreateEmailNotificationRequest $request): JsonResponse
    {
        Assert::notNull($request->email, 'Email should not be null');
        Assert::notNull($request->message, 'Message should not be null');
        Assert::notNull($request->subject, 'Subject should not be null');

        $command = new CreateEmailNotificationCommand(
            email: $request->email,
            subject: $request->subject,
            message: $request->message
        );

        try {
            $this->messageBus->dispatch($command);
        } catch (MessageException) {
            return new JsonResponse(status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
