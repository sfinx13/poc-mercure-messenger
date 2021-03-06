<?php

namespace App\Tests\Service\Generator;

use App\Entity\NotificationType;
use App\Messenger\Message\ExportMessage;
use App\Model\FileInfo;
use App\Service\Generator\FileGenerator;
use App\Service\Notification\Notifier;
use App\Utils\RandomFloatProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileGeneratorTest extends KernelTestCase
{
    public string $filename = 'export_user_test_1641597896.csv';
    public string $path = '';

    public function setUp(): void
    {
        $this->path = static::getContainer()->getParameter('kernel.project_dir')
        . static::getContainer()->getParameter('destination_folder');

        if (file_exists($this->path . $this->filename)) {
            unlink($this->path . $this->filename);
        }
    }

    public function testFileGenerated(): void
    {
        $parameterBag = static::getContainer()->get(ParameterBagInterface::class);
        $randomFloatProvider = $this->createMock(RandomFloatProvider::class);
        $notifier = $this->createMock(Notifier::class);

        $fileGenerator = new FileGenerator($randomFloatProvider, $parameterBag, $notifier);

        $exportMessage = (new ExportMessage())
            ->setUsername('user_test')
            ->setStartDate(new \DateTime())
            ->setFilename($this->filename)
            ->setTemplate(NotificationType::TEMPLATE_EXPORT_START)
            ->setInterval(1);

        $fileGeneratedInfo = $fileGenerator->initFrom($exportMessage)->generate();

        $this->assertFileExists($fileGeneratedInfo->getFilepath(), 'given filename does not exists');
        $this->assertTrue($fileGeneratedInfo->getFilename() === $this->filename);
        $this->assertNotEmpty($fileGeneratedInfo->getFilesize());
    }

    public function testFileGeneratedMocked()
    {
        $fileGenerator = $this->createMock(FileGenerator::class);

        $fileInfo = (new FileInfo())
            ->setFilepath($this->path)
            ->setFilename($this->filename)
            ->setGeneratedAt(time())
            ->setFilesize('1 Ko');

        $fileGenerator->method('generate')->willReturn($fileInfo);

        $this->assertEquals($fileInfo, $fileGenerator->generate());
    }
}
