<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\Create;

use App\EmailNotification\Shared\Model\EmailNotification;
use DaveLiddament\PhpLanguageExtensions\NamespaceVisibility;

#[NamespaceVisibility('App\EmailNotification')]
interface EmailNotificationFactoryInterface
{
    public function create(CreateEmailNotificationDto $dto): EmailNotification;
}
