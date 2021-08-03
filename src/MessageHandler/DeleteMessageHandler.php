<?php

namespace App\MessageHandler;

use App\Manager\FileManager;
use App\Manager\NotificationManager;
use App\Message\DeleteMessage;
use App\Notification\Notification;
use App\Notification\Notifier;
use App\Service\Counter;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class DeleteMessageHandler implements MessageHandlerInterface
{
    private Counter $counter;

    private FileManager $fileManager;

    private NotificationManager $notificationManager;

    private Notifier $notifier;

    private Security $security;

    public function __construct(
        Counter $counter,
        FileManager $fileManager,
        NotificationManager $notificationManager,
        Notifier $notifier,
        Security $security
    ) {
        $this->counter = $counter;
        $this->fileManager = $fileManager;
        $this->notificationManager = $notificationManager;
        $this->notifier = $notifier;
        $this->security = $security;
    }

    public function __invoke(DeleteMessage $deleteMessage)
    {
        $this->fileManager->removeFromUser($this->security->getUser());
        $notification = $this->notificationManager->createFrom($deleteMessage);

        $this->notifier->send(
            new Notification(
                ['files/'.$deleteMessage->getUsername()],
                ['message' => 'All files has been deleted'],
                true,
                'files-deleted'
            )
        );

        $username = $deleteMessage->getUsername();
        $this->notifier->send(
            new Notification(
            ['files/'.$username],
            ['counter' => $this->counter->reset($username)],
            true,
            'files-deleted'
        )
        );

        $data = [
            'id' => $notification->getId(),
            'message' => $notification->getContent()
        ];

        $this->notifier->send(new Notification(['notifications'], $data, false));
    }
}
