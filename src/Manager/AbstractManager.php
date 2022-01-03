<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager implements ManagerInterface
{
    public function __construct(protected EntityManagerInterface $entityManager) {}

    public function save($entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->flush();
        }
    }

    public function remove($entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
