<?php

namespace App\Factory;

use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Message\MessageInterface;
use App\Repository\NotificationTypeRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationFactory
{
    public function __construct(private NotificationTypeRepository $notificationTypeRepository)
    {
    }

    public function createInstanceFrom(MessageInterface $message): Notification
    {
        $notificationType = $this->notificationTypeRepository->findOneBy(['slug' => $message->getTemplate()]);
        if (!$notificationType instanceof NotificationType) {
            return new Notification();
        }

        $content = str_replace(["[USER]", "[FILENAME]"], [
            $message->getUsername(), $message->getFilename(),
        ], $notificationType->getTemplate());

        return (new Notification())
            ->setContent($content)
            ->setLink($message->getFilename())
            ->setUsername($message->getUsername())
            ->setSender('app');
    }

    public function createInstanceFromUser(UserInterface $user, string $template): Notification
    {
        $notificationType = $this->notificationTypeRepository->findOneBy(['slug' => $template]);
        if (!$notificationType instanceof NotificationType) {
            return new Notification();
        }

        $content = str_replace("[USER]", $user->getUserIdentifier(), $notificationType->getTemplate());

        return (new Notification())
            ->setContent($content)
            ->setUsername($user->getUserIdentifier())
            ->setSender('app');
    }
}
