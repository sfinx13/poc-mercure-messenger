<?php

namespace App\Messenger\MessageHandler;

use App\Entity\NotificationType;
use App\Model\FileInfo;
use App\Messenger\Message\ExportMessage;
use App\Repository\NotificationRepository;
use App\Manager\FileManager;
use App\Manager\NotificationManager;
use App\Service\Generator\FileGenerator;
use App\Service\Notification\Notification;
use App\Service\Notification\NotifierInterface;
use App\Service\Counter\Counter;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExportMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $router,
        private NotifierInterface $notifier,
        private Counter $counter,
        private FileGenerator $fileGenerator,
        private FileManager $fileManager,
        private NotificationManager $notificationManager,
        private NotificationRepository $notificationRepository
    ) {
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
            'link' => $this->router->generate('download_file', [
                'filename' => $exportMessage->getFilename()
            ]),
            'count_notifications' => $this->notificationRepository->countNotificationNotProcessed()
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
