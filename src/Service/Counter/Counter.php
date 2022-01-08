<?php

namespace App\Service\Counter;

use Psr\Cache\CacheItemPoolInterface;

class Counter
{
    public function __construct(private CacheItemPoolInterface $cache,
                                private CountableMessageInterface $countableMessage)
    {
    }

    public function current($key): void
    {
        $nbMessage = $this->countableMessage->count();
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
