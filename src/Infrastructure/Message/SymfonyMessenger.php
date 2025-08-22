<?php

declare(strict_types=1);

namespace App\Infrastructure\Message;

use App\Shared\Message\EventBusInterface;
use App\Shared\Message\MessageBusInterface;
use App\Shared\Message\MessageException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface as SymfonyMessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final readonly class SymfonyMessenger implements MessageBusInterface, EventBusInterface
{
    public function __construct(private SymfonyMessageBusInterface $messageBus)
    {
    }

    public function dispatch(object $message): void
    {
        try {
            $this->messageBus->dispatch($message);
        } catch (ExceptionInterface $e) {
            throw new MessageException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function dispatchAfterCurrentBusStamp(object $message): void
    {
        try {
            $this->messageBus->dispatch($message, [new DispatchAfterCurrentBusStamp()]);
        } catch (ExceptionInterface $e) {
            throw new MessageException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
