<?php

declare(strict_types=1);

namespace App\EmailNotification\Features\GetNotifications;

use DaveLiddament\PhpLanguageExtensions\NamespaceVisibility;

#[NamespaceVisibility('App\EmailNotification')]
interface GetEmailNotificationsInterface
{
    /**
     * @return list<array{
     *      uuid: string,
     *      recipients: list<string>,
     *      subject: string,
     *      body: string,
     *      status: string,
     *      createdAt: string
     * }>
     */
    public function execute(): array;
}
