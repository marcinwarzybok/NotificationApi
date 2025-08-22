<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;

trait DoctrineRepositoryTrait
{
    private readonly EntityManagerInterface $entityManager;

    public function saveDeferred(object $entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function save(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
