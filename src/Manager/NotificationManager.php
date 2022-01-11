<?php

namespace App\Manager;

use App\Entity\Notification;
use App\Factory\NotificationFactory;
use App\Messenger\Message\MessageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationManager extends AbstractManager
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private NotificationFactory $notificationFactory
    ) {
        parent::__construct($entityManager);
    }

    public function createFrom(MessageInterface $message): Notification
    {
        $notification = $this->notificationFactory->createInstanceFrom($message);
        $this->save($notification);

        return $notification;
    }

    public function createFromUser(UserInterface $user, string $notificationTemplate): Notification
    {
        $notification = $this->notificationFactory->createInstanceFromUser($user, $notificationTemplate);
        $this->save($notification);

        return $notification;
    }
}
