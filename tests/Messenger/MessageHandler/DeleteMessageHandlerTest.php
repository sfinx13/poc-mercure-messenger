<?php

namespace App\Messenger\MessageHandler;

use App\Entity\User;
use App\Manager\FileManager;
use App\Manager\NotificationManager;
use App\Messenger\Message\DeleteMessage;
use App\Service\Counter\Counter;
use App\Service\Notification\Notifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class DeleteMessageHandlerTest extends TestCase
{
    public function testProcessDeleteMessage()
    {
        $counterMock = $this->createMock(Counter::class);
        $fileManagerMock = $this->createMock(FileManager::class);
        $notificationManagerMock = $this->createMock(NotificationManager::class);
        $notifierMock = $this->createMock(Notifier::class);
        $securityMock = $this->createMock(Security::class);

        $securityMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn((new User())->setUsername('John Doe'));

        $notifierMock
            ->expects($this->atLeast(3))
            ->method('send')
            ->willReturn('urn:uuid:7b26df22-bbc5-466f-b517-05aaa3b12f4a');

        $fileManagerMock
            ->expects($this->once())
            ->method('removeFromUser');

        $notificationManagerMock
            ->expects($this->once())
            ->method('createFrom')
            ->with($this->deleteMessage());

        $deleteMessageHandler = new DeleteMessageHandler(
            $counterMock,
            $fileManagerMock,
            $notificationManagerMock,
            $notifierMock,
            $securityMock
        );

        $deleteMessageHandler($this->deleteMessage());
    }

    private function deleteMessage()
    {
        return (new DeleteMessage())
            ->setUsername('John Doe')
            ->setFilename('sample.csv')
            ->setTemplate('[USER] has been deleted his files');
    }
}
