<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\GetNotifications\Entrypoint\Http;

use App\EmailNotification\Features\GetNotifications\GetEmailNotificationsInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final readonly class GetEmailNotificationsAction
{
    public function __construct(private GetEmailNotificationsInterface $getAllEmailNotifications)
    {
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->getAllEmailNotifications->execute());
    }
}
