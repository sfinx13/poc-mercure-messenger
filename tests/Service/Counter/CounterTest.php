<?php

namespace App\Tests\Service\Counter;

use PHPUnit\Framework\TestCase;
use App\Service\Counter\CountableMessage;
use App\Service\Counter\Counter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CounterTest extends TestCase
{
    public function testInscreasedCounter()
    {
        $countableMessageMock = $this->createMock(CountableMessage::class);

        $countableMessageMock
            ->expects($this->once())
            ->method('count')
            ->willReturn(3);

        $cache = new ArrayAdapter();
        $counter = $cache->getItem('counter_dummy');
        $counter->set($countableMessageMock->count());
        $cache->save($counter);

        $counter = new Counter($cache, $countableMessageMock);
        $counter->increase('dummy');
        $expectedCacheValue = (int)$cache->getItem('counter_dummy')->get();

        $this->assertTrue(4 === $expectedCacheValue, "Expected value is {$expectedCacheValue}");
    }

    public function testDecreasedCounter()
    {
        $countableMessageMock = $this->createMock(CountableMessage::class);
        $countableMessageMock
            ->expects($this->once())
            ->method('count')
            ->willReturn(10);

        $cache = new ArrayAdapter();
        $counter = $cache->getItem('counter_dummy');
        $counter->set($countableMessageMock->count());
        $cache->save($counter);

        $counter = new Counter($cache, $countableMessageMock);
        $counter->decrease('dummy');
        $expectedCacheValue = $cache->getItem('counter_dummy')->get();
        $this->assertTrue(9 === $expectedCacheValue, "Expected value is {$expectedCacheValue}");
    }


    public function testResetCounter()
    {
        $countableMessageMock = $this->createMock(CountableMessage::class);
        $countableMessageMock
            ->expects($this->once())
            ->method('count')
            ->willReturn(150);

        $cache = new ArrayAdapter();
        $counter = $cache->getItem('counter_dummy');
        $counter->set($countableMessageMock->count());
        $cache->save($counter);

        $counter = new Counter($cache, $countableMessageMock);
        $counter->reset('dummy');

        $expectedCacheValue = $cache->getItem('counter_dummy')->get();
        $this->assertTrue(0 === $expectedCacheValue, "Expected value is {$expectedCacheValue}");
    }

    public function testCurrentCounterWithCoutableItems()
    {
        $countableMessageMock = $this->createMock(CountableMessage::class);
        $countableMessageMock
            ->expects($this->atLeastOnce())
            ->method('count')
            ->willReturn(1);

        $cache = new ArrayAdapter();
        $counter = $cache->getItem('counter_dummy');
        $counter->set($countableMessageMock->count());
        $cache->save($counter);

        $counter = new Counter($cache, $countableMessageMock);
        $counter->current('dummy');


        $expectedCacheValue = $cache->getItem('counter_dummy')->get();
        $this->assertTrue(1 === $expectedCacheValue, "Expected value is {$expectedCacheValue}");
    }

    public function testCurrentCounterWithEmptyItems()
    {
        $countableMessageMock = $this->createMock(CountableMessage::class);
        $countableMessageMock
            ->expects($this->atLeastOnce())
            ->method('count')
            ->willReturn(0);

        $cache = new ArrayAdapter();
        $counter = $cache->getItem('counter_dummy');
        $counter->set($countableMessageMock->count());
        $cache->save($counter);

        $counter = new Counter($cache, $countableMessageMock);
        $counter->current('dummy');


        $expectedCacheValue = $cache->getItem('counter_dummy')->get();
        $this->assertTrue(0 === $expectedCacheValue, "Expected value is {$expectedCacheValue}");
    }
}
