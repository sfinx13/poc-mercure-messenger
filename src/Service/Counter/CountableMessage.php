<?php

namespace App\Service\Counter;

use Doctrine\ORM\EntityManagerInterface;

class CountableMessage implements CountableMessageInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function count(): int
    {
        $connection = $this->entityManager->getConnection();
        $request = "SELECT count(*) as nb_message FROM messenger_messages where delivered_at is null;";
        $statement = $connection->prepare($request);
        $result = $statement->executeQuery();
        $data = $result->fetchAssociative();

        return (int)$data['nb_message'];
    }
}