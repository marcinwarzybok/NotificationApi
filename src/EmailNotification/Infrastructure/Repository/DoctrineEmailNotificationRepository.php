<?php

declare(strict_types=1);

namespace App\EmailNotification\Infrastructure\Repository;

use App\EmailNotification\Shared\EmailNotificationRepositoryInterface;
use App\EmailNotification\Shared\Exception\EmailNotificationNotFoundException;
use App\EmailNotification\Shared\Model\EmailNotification;
use App\Infrastructure\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailNotification>
 */
final class DoctrineEmailNotificationRepository extends ServiceEntityRepository implements EmailNotificationRepositoryInterface
{
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailNotification::class);

        $this->entityManager = $this->getEntityManager();
    }

    public function getByUuid(string $uuid): EmailNotification
    {
        $notification = $this->findOneBy(['uuid' => $uuid]);

        if (null === $notification) {
            throw EmailNotificationNotFoundException::byUuid($uuid);
        }

        return $notification;
    }

    public function findAll(): array
    {
        /** @var list<EmailNotification> $results */
        $results = parent::findAll();

        return $results;
    }
}
