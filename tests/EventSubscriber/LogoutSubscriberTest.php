<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\LogoutSubscriber;
use App\Manager\NotificationManager;
use App\Service\Notification\Notifier;
use App\Entity\Notification as EntityNotification;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriberTest extends TestCase
{
    public function testEventSubscription()
    {
        $this->assertArrayHasKey(LogoutEvent::class, LogoutSubscriber::getSubscribedEvents());
    }

    public function testOnLogoutSendNotificationCallMethod()
    {
        $notifierMock = $this->createMock(Notifier::class);
        $notificationManagerMock = $this->createMock(NotificationManager::class);

        $notifierMock->expects($this->once())
            ->method('send')
            ->willReturn('urn:uuid:7b26df22-bbc5-466f-b517-05aaa3b12f4a');

        $notificationManagerMock->expects($this->once())
            ->method('createFromUser')
            ->willReturn((new EntityNotification())->setContent('[USER] has been logout'));

        $logoutEvent = new LogoutEvent(
            new Request(),
            new UsernamePasswordToken(new InMemoryUser('foo', 'bar'), 'main')
        );

        $logoutSubscriber = new LogoutSubscriber($notificationManagerMock, $notifierMock);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($logoutSubscriber);
        $dispatcher->dispatch($logoutEvent);
    }
}