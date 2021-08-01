<?php

namespace App\Manager;

use App\Factory\NotificationFactory;
use App\Message\MessageInterface;
use App\Service\Publisher;
use Doctrine\ORM\EntityManagerInterface;

class NotificationManager extends AbstractManager
{
    protected NotificationFactory $notificationFactory;
    protected Publisher $publisher;

    public function __construct(EntityManagerInterface $entityManager,
                                NotificationFactory    $notificationFactory,
                                Publisher              $publisher
    ) {
        parent::__construct($entityManager);
        $this->notificationFactory = $notificationFactory;
        $this->publisher = $publisher;
    }

    public function createNotification(string $notificationTemplate, MessageInterface $message = null): void
    {
        $notification = $this->notificationFactory->createInstance($notificationTemplate, $message);
        $this->save($notification);
    }
}