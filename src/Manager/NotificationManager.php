<?php

namespace App\Manager;

use App\Entity\Notification;
use App\Factory\NotificationFactory;
use App\Message\MessageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationManager extends AbstractManager
{
    private NotificationFactory $notificationFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationFactory    $notificationFactory
    ) {
        parent::__construct($entityManager);
        $this->notificationFactory = $notificationFactory;
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
