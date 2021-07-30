<?php

namespace App\MessageHandler;

use App\Generator\FileGenerator;
use App\Manager\FileManager;
use App\Message\ExportMessage;
use App\Entity\File;
use App\Service\Counter;
use App\Service\Publisher;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ExportMessageHandler implements MessageHandlerInterface
{
    protected ParameterBagInterface $parameterBag;
    protected Publisher $publisher;
    protected Counter $counter;
    protected FileGenerator $fileGenerator;
    protected FileManager $fileManager;
    protected LoggerInterface $logger;

    public function __construct(
        ParameterBagInterface $parameterBag,
        Publisher $publisher,
        Counter $counter,
        FileGenerator $fileGenerator,
        FileManager $fileManager,
        LoggerInterface $logger
    ) {
        $this->parameterBag = $parameterBag;
        $this->publisher = $publisher;
        $this->counter = $counter;
        $this->fileGenerator = $fileGenerator;
        $this->fileManager = $fileManager;
        $this->logger = $logger;
    }

    public function __invoke(ExportMessage $exportMessage)
    {
        $this->fileGenerator->initFrom($exportMessage)->generate();

        $size = $this->fileGenerator->getFilesize();
        $filename = $this->fileGenerator->getFilename();
        $generatedAt = $this->fileGenerator->getGeneratedAt();

        $file = $this->fileManager->findOneBy(['filename' => $filename]);
        $file
            ->setStatus(File::STATUS_READY)
            ->setSize($size)
            ->setExportedAt((new \DateTimeImmutable())->setTimestamp($generatedAt));
        $this->fileManager->save($file);

        $username = $exportMessage->getUser()->getUserIdentifier();

        $topic = $this->parameterBag->get('topic_url') . '/files/' . $username;
        $data = [
            'timestamp' => $generatedAt,
            'filename' => $filename,
            'size' => $size
        ];
        $this->publisher->publish($topic, $data, true, 'creating-file');

        $data = ['counter' => $this->counter->decrease($username)];
        $this->publisher->publish($topic, $data, true, 'counter');
    }
}
