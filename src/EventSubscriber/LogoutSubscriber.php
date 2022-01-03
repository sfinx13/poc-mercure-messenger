<?php

namespace App\EventSubscriber;

use App\Manager\NotificationManager;
use App\Service\Notification\Notification;
use App\Service\Notification\Notifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(private NotificationManager $notificationManager, private Notifier $notifier) {}

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout'
        ];
    }

    public function onLogout(LogoutEvent $logoutEvent): void
    {
        $notification = $this->notificationManager->createFromUser(
            $logoutEvent->getToken()->getUser(),
            'logout'
        );

        $data['message'] = $notification->getContent();
        $this->notifier->send(new Notification(['notifications'], $data, false));
    }
}
