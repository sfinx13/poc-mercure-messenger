<?php

namespace App\Generator;

use App\Manager\FileManager;
use App\Message\ExportMessage;
use App\Service\Publisher;
use App\Utils\MathHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Uid\Uuid;

class FileGenerator
{
    protected string $filename;
    protected string $filepath;
    protected string $filesize;
    protected \DateInterval $interval;
    protected string $generatedAt;
    protected string $userId;

    protected string $destinationFolder;

    protected FileManager $fileManager;
    protected ParameterBagInterface $parameterBag;
    protected Publisher $publisher;
    protected AdapterInterface $cache;
    protected LoggerInterface $logger;

    public function __construct(
        FileManager $fileManager,
        ParameterBagInterface $parameterBag,
        Publisher $publisher,
        LoggerInterface $logger
    ) {
        $this->fileManager = $fileManager;
        $this->parameterBag = $parameterBag;
        $this->publisher = $publisher;
        $this->logger = $logger;
        $this->destinationFolder = $this->parameterBag->get('kernel.project_dir') . $this->parameterBag->get('destination_folder');
    }

    public function initFrom(ExportMessage $exportMessage): self
    {
        $this->filename = $exportMessage->getFilename();
        $this->filepath = $this->destinationFolder . $this->filename;
        $this->interval = date_interval_create_from_date_string($exportMessage->getInterval() . ' seconds');
        $this->userId = $exportMessage->getUser()->getUserIdentifier();
        return $this;
    }

    public function generate(): void
    {
        $topics = [
            $this->parameterBag->get('topic_url').'/files/'. $this->userId
        ];
        $filesystem = new Filesystem();
        $this->logger->info($this->destinationFolder);

        if (!$filesystem->exists($this->destinationFolder)) {
            $filesystem->mkdir($this->destinationFolder);
        }

        $filesystem->touch($this->filepath);
        $stopDate = (new \DateTime())->add($this->interval);
        $startDate = new \DateTime();

        while (new \DateTime() < $stopDate) {
            $now = \DateTime::createFromFormat('U.u', microtime(true));

            if ($now !== false) {
                $line = $now->format("Y-m-d H:i:s.u") . ';' . MathHelper::randomFloat(0, 1);
                $filesystem->appendToFile($this->filepath, $line . PHP_EOL);

                $data = ['percentage' => abs($this->getPercentage($startDate, $stopDate) - 100)];
                $this->publisher->publish($topics, $data, true, 'progress-bar');
            }
        }

        $file = new File($this->filepath);

        $this->filesize =  round($file->getSize() / 1024) . ' Ko';
        $this->generatedAt = time();
    }

    public function getFilesize(): string
    {
        return $this->filesize;
    }

    public function getGeneratedAt(): string
    {
        return $this->generatedAt;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    private function getPercentage(\DateTime $startDate, \DateTime $stopDate): float
    {
        return (($stopDate->getTimestamp() -
                    (new \DateTime())->getTimestamp()) * 100) /
            ($stopDate->getTimestamp() - $startDate->getTimestamp());
    }
}
