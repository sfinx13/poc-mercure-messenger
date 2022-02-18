<?php

namespace App\Messenger\MessageHandler;

use App\Entity\NotificationType;
use App\Manager\FileManager;
use App\Manager\NotificationManager;
use App\Messenger\Message\ExportMessage;
use App\Model\FileInfo;
use App\Repository\NotificationRepository;
use App\Service\Counter\Counter;
use App\Service\Generator\FileGenerator;
use App\Service\Notification\Notifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExportMessageHandlerTest extends TestCase
{
    public function testProcessExportMessage()
    {
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $notifierMock = $this->createMock(Notifier::class);
        $counterMock = $this->createMock(Counter::class);
        $fileGeneratorMock = $this->createMock(FileGenerator::class);
        $fileManagerMock = $this->createMock(FileManager::class);
        $notificationManagerMock = $this->createMock(NotificationManager::class);
        $notificationRepositoryMock = $this->createMock(NotificationRepository::class);

        $notifierMock
            ->expects($this->atLeast(4))
            ->method('send')
            ->willReturn('urn:uuid:7b26df22-bbc5-466f-b517-05aaa3b12f4a');

        $fileGeneratorMock
            ->expects($this->once())
            ->method('initFrom')
            ->willReturn($fileGeneratorMock);

        $fileGeneratorMock
            ->expects($this->any())
            ->method('generate')
            ->willReturn(new FileInfo());

        $this->assertInstanceOf(FileInfo::class, $fileGeneratorMock->generate());

        $exportMessageHandler = new ExportMessageHandler(
            $urlGeneratorMock,
            $notifierMock,
            $counterMock,
            $fileGeneratorMock,
            $fileManagerMock,
            $notificationManagerMock,
            $notificationRepositoryMock
        );

        $exportMessageHandler($this->getExportMessage());
    }


    private function getExportMessage(): ExportMessage
    {
        return  (new ExportMessage())
            ->setUsername('john doe')
            ->setStartDate(new \DateTime())
            ->setFilename('sample.csv')
            ->setInterval(5)
            ->setData([
                'notification_id' => 1,
                'notification_content' => 'dummy content',
                'file_id' => 1
            ])
            ->setTemplate(NotificationType::TEMPLATE_EXPORT_START);
    }
}
