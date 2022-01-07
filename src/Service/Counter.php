<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Counter
{
    public function __construct(private AdapterInterface $cache, private EntityManagerInterface $entityManager)
    {
    }

    public function current($key): void
    {
        $connection = $this->entityManager->getConnection();
        $request = "SELECT count(*) as nb_message FROM messenger_messages where delivered_at is null;";
        $statement = $connection->prepare($request);
        $result = $statement->executeQuery();
        $data = $result->fetchAssociative();
        $nbMessage = (int)$data['nb_message'];

        if (0 === $nbMessage) {
            $this->cache->clear();
        }

        $counter = $this->cache->getItem('counter_' . $key);
        $counter->set($nbMessage);
        $this->cache->save($counter);
    }

    public function increase($key): int
    {
        $counter = $this->cache->getItem('counter_' . $key);
        $counter->set(!$counter->isHit() ? 1 : (int)$counter->get() + 1);
        $this->cache->save($counter);

        return (int)$counter->get();
    }

    public function decrease($key): int
    {
        $counter = $this->cache->getItem('counter_' . $key);
        if ($counter->isHit() && $counter->get() > 0) {
            $counter->set((int)$counter->get() - 1);
            $this->cache->save($counter);
        }
        return (int)$counter->get();
    }

    public function reset($key): int
    {
        $counter = $this->cache->getItem('counter_' . $key);
        $counter->set(0);
        $this->cache->save($counter);

        return (int)$counter->get();
    }
}
