<?php

namespace App\MessageHandler;

use App\Entity\Notification as EntityNotification;
use App\Entity\NotificationType;
use App\Generator\FileGenerator;
use App\Manager\FileManager;
use App\Manager\NotificationManager;
use App\Message\ExportMessage;
use App\Model\FileInfo;
use App\Notification\Notification;
use App\Notification\Notifier;
use App\Service\Counter;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExportMessageHandler implements MessageHandlerInterface
{
    private UrlGeneratorInterface $router;

    private Notifier $notifier;

    private Counter $counter;

    private FileGenerator $fileGenerator;

    private FileManager $fileManager;

    private NotificationManager $notificationManager;

    public function __construct(
        UrlGeneratorInterface $router,
        Notifier             $notifier,
        Counter               $counter,
        FileGenerator         $fileGenerator,
        FileManager           $fileManager,
        NotificationManager   $notificationManager
    ) {
        $this->router = $router;
        $this->notifier = $notifier;
        $this->counter = $counter;
        $this->fileGenerator = $fileGenerator;
        $this->fileManager = $fileManager;
        $this->notificationManager = $notificationManager;
    }

    public function __invoke(ExportMessage $exportMessage)
    {
        $username = $exportMessage->getUsername();
        $this->notifier->send(
            new Notification(
                ['notifications'],
                $this->getNotificationData($exportMessage),
                false
            )
        );

        $fileInfo = $this->fileGenerator->initFrom($exportMessage)->generate();
        $fileId = $exportMessage->getData()['file_id'];
        $this->fileManager->updateFrom($fileId, $fileInfo);
        $this->notifier->send(
            new Notification(
                ['files/' . $username],
                $this->getFileData($fileInfo),
                true,
                'file-created'
            )
        );

        $this->notifier->send(
            new Notification(
                ['files/' . $username],
                $this->getCountData($username),
                true,
                'counter'
            )
        );

        $exportMessage->setTemplate(NotificationType::TEMPLATE_EXPORT_END);
        $notification = $this->notificationManager->createFrom($exportMessage);
        $exportMessage->setData([
            'notification_id' => $notification->getId(),
            'notification_content' => $notification->getContent()
        ]);

        $this->notifier->send(
            new Notification(
                ['notifications'],
                $this->getNotificationData($exportMessage),
                false
            )
        );
    }

    private function getNotificationData(ExportMessage $exportMessage): array
    {
        return [
            'id' => $exportMessage->getData()['notification_id'],
            'message' => $exportMessage->getData()['notification_content'],
            'link' => $this->router->generate('download_file', ['filename' => $exportMessage->getFilename()])
        ];
    }

    private function getFileData(FileInfo $fileInfo): array
    {
        return [
            'timestamp' => $fileInfo->getGeneratedAt(),
            'filename' => $fileInfo->getFilename(),
            'size' => $fileInfo->getFilesize()
        ];
    }

    private function getCountData(string $username): array
    {
        return ['counter' => $this->counter->decrease($username)];
    }
}
