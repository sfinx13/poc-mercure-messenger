<?php

namespace App\Tests\Service\Notification;

use App\Service\Notification\Notification;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testNotificationOnMessageEvent(): void
    {
        $notification = new Notification(
            [
            'https://www.example.com/user/12/files',
            'https://www.example.com/message'],
            ['message' => 'Hello world']
        );

        $this->assertCount(2, $notification->getTopics());
        $this->assertEquals([
            'https://www.example.com/user/12/files',
            'https://www.example.com/message'
        ], $notification->getTopics());

        $this->assertEquals('Hello world', $notification->getData()['message']);
    }

    public function testNotificationOnCustomEvent(): void
    {
        $notification = (new Notification([
            'https://www.example.com/user/12/files',
            'https://www.example.com/message'
        ], ['message' => 'Hello moon']));

        $notification->setContent('Hello moon')
            ->setEventType('onTestApplied')
            ->setPrivate(true)
            ->setTopics([
                'https://www.example.com/user/12/files',
                'https://www.example.com/message',
                'https://www.example.com/cart'
            ]);

        $this->assertCount(3, $notification->getTopics());
        $this->assertEquals('Hello moon', $notification->getContent());
        $this->assertEquals('Hello moon', $notification->getData()['message']);
    }
}
