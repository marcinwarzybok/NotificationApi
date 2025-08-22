<?php

declare(strict_types=1);

namespace App\Infrastructure\Message;

use App\Shared\Mailer\SendingEmailException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Messenger\AbstractDoctrineMiddleware;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class DoctrineTransactionMiddleware extends AbstractDoctrineMiddleware
{
    protected function handleForManager(EntityManagerInterface $entityManager, Envelope $envelope, StackInterface $stack): Envelope
    {
        $entityManager->getConnection()->beginTransaction();
        try {
            $envelope = $stack->next()->handle($envelope, $stack);
            $entityManager->flush();
            $entityManager->getConnection()->commit();

            return $envelope;
        } catch (\Throwable $exception) {
            if ($exception->getPrevious() instanceof SendingEmailException) {
                $entityManager->flush();
                $entityManager->getConnection()->commit();
            } else {
                $entityManager->getConnection()->rollBack();
            }

            throw $exception;
        }
    }
}
