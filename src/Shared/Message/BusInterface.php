<?php

declare(strict_types=1);

namespace App\Shared\Message;

interface BusInterface
{
    /** @throws MessageException */
    public function dispatch(object $message): void;

    /** @throws MessageException */
    public function dispatchAfterCurrentBusStamp(object $message): void;
}
