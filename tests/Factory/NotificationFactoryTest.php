<?php

namespace App\Tests\Factory;

use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Factory\NotificationFactory;
use App\Messenger\Message\ExportMessage;
use App\Repository\NotificationTypeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

class NotificationFactoryTest extends KernelTestCase
{
    public function testCreateInstanceFromReturnNotEmptyNotification(): void
    {
        $path = static::getContainer()->getParameter('kernel.project_dir')
            . '/src/DataFixtures/Data/notification-type.yaml';

        $dummyData = Yaml::parseFile($path);
        $dummyNotificationType = reset($dummyData['notification-types']);

        $notificationType = (new NotificationType())
            ->setName($dummyNotificationType['name'])
            ->setType($dummyNotificationType['type'])
            ->setTemplate($dummyNotificationType['template'])
            ->setSlug(str_replace(' ', '-', strtolower($dummyNotificationType['name'])));

        $notificationTypeRepository = $this->createMock(NotificationTypeRepository::class);

        $notificationTypeRepository
            ->expects($this->any())
            ->method('findOneBy')
            ->willReturn($notificationType);

        $exportMessage = $this->getExportMessage();
        $notification = (new NotificationFactory($notificationTypeRepository))->createInstanceFrom($exportMessage);

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertTrue($notification->getUsername() === $exportMessage->getUsername());
        $this->assertStringContainsString($exportMessage->getUsername(), $notification->getContent());
        $this->assertTrue($notification->getLink() === $exportMessage->getFilename());
        $this->assertTrue($notification->getSender() === 'app');
    }

    public function testCreateInstanceFromReturnEmptyNotification(): void
    {
        $notificationTypeRepository = $this->createMock(NotificationTypeRepository::class);

        $notificationTypeRepository
            ->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $notification = (new NotificationFactory($notificationTypeRepository))
            ->createInstanceFrom($this->getExportMessage());

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertTrue($notification->isActive());
    }

    private function getExportMessage(): ExportMessage
    {
        return  (new ExportMessage())
            ->setUsername('john doe')
            ->setStartDate(new \DateTime())
            ->setFilename('sample.csv')
            ->setInterval(5)
            ->setTemplate(NotificationType::TEMPLATE_EXPORT_START);
    }
}
