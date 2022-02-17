<?php

namespace App\Tests\Factory;

use App\Entity\File;
use App\Entity\NotificationType;
use App\Entity\User;
use App\Factory\FileFactory;
use App\Messenger\Message\ExportMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

class FileFactoryTest extends KernelTestCase
{
    public function testCreateInstanceFileFromExportMessage()
    {
        self::bootKernel();

        $parameterBag = static::getContainer()->get(ParameterBagInterface::class);

        $securityMock = $this->createMock(Security::class);
        $securityMock->expects($this->once())
            ->method('getUser')
            ->willReturn((new User())->setUsername('John Doe'));

        $fileFactory = new FileFactory($parameterBag, $securityMock);
        $exportMessage = $this->getExportMessage();
        $file = $fileFactory->createInstanceFrom($this->getExportMessage());

        $this->assertInstanceOf(File::class, $file);

        $this->assertTrue($exportMessage->getFilename() === $file->getFilename());
        $this->assertTrue($exportMessage->getUsername() === $file->getUser()->getUsername());
        $this->assertTrue(File::STATUS_DRAFT === $file->getStatus());
        $this->assertStringContainsString(
            $parameterBag->get('kernel.project_dir') . '/public/csv',
            $file->getFilepath()
        );
    }

    private function getExportMessage(): ExportMessage
    {
        return  (new ExportMessage())
            ->setUsername('John Doe')
            ->setStartDate(new \DateTime())
            ->setFilename('sample.csv')
            ->setInterval(5)
            ->setTemplate(NotificationType::TEMPLATE_EXPORT_START);
    }
}
