<?php

declare(strict_types=1);

namespace App\EmailNotification\Shared;

use App\EmailNotification\Shared\Model\EmailNotification;
use App\Shared\Model\EntityNotFoundException;
use DaveLiddament\PhpLanguageExtensions\NamespaceVisibility;

#[NamespaceVisibility('App\EmailNotification')]
interface EmailNotificationRepositoryInterface
{
    public function saveDeferred(EmailNotification $notification): void;

    public function save(EmailNotification $notification): void;

    /** @throws EntityNotFoundException */
    public function getByUuid(string $uuid): EmailNotification;

    /** @return EmailNotification[] */
    public function findAll(): array;
}
