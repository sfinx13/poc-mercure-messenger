<?php

namespace App\Factory;

use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Message\MessageInterface;
use App\Repository\NotificationTypeRepository;
use Symfony\Component\Security\Core\Security;

class NotificationFactory
{
    private NotificationTypeRepository $notificationTypeRepository;
    private Security $security;

    public function __construct(NotificationTypeRepository $notificationTypeRepository, Security $security)
    {
        $this->notificationTypeRepository = $notificationTypeRepository;
        $this->security = $security;
    }

    public function createInstance(string $notificationTemplate, MessageInterface $message = null): Notification
    {
        $notificationType = $this->notificationTypeRepository->findOneBy(['slug' => $notificationTemplate]);
        if (!$notificationType instanceof NotificationType) {
            return new Notification();
        }

        $template = $notificationType->getTemplate();
        if ($message !== null) {
            return $this->createInstanceFromMessage($message, $template);
        }

        return $this->createInstanceFromUser($this->security->getUser(), $template);
    }

    private function createInstanceFromMessage(MessageInterface $message, string $template): Notification
    {
        $content = str_replace(
            ["{{user}}", "{{filename}}"],
            [$message->getUser()->getUserIdentifier(), $message->getFilename()],
            $template);

        return (new Notification())
            ->setContent($content)
            ->setUser($message->getUser())
            ->setSender('app');
    }

    private function createInstanceFromUser(User $user, string $template): Notification
    {
        $content = str_replace("{{user}}", $user->getUserIdentifier(), $template);

        return (new Notification())
            ->setContent($content)
            ->setUser($user)
            ->setSender('app');
    }
}
