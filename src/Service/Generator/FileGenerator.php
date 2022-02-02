<?php

namespace App\Service\Generator;

use App\Messenger\Message\ExportMessage;
use App\Model\FileInfo;
use App\Service\Notification\NotifierInterface;
use App\Utils\RandomFloatProviderInterface;
use App\Service\Notification\Notifier;
use App\Service\Notification\Notification;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class FileGenerator implements FileGeneratorInterface
{
    private string $filename;
    private string $filepath;
    private \DateInterval $interval;
    private string $generatedBy;

    private string $destinationFolder;

    public function __construct(
        private RandomFloatProviderInterface $randomFloatProvider,
        private ParameterBagInterface $parameterBag,
        private NotifierInterface $notifier
    ) {
    }

    public function initFrom(ExportMessage $exportMessage): self
    {
        $this->destinationFolder = $this->parameterBag->get('kernel.project_dir')
            . $this->parameterBag->get('destination_folder');

        $this->filename = $exportMessage->getFilename();
        $this->filepath = $this->destinationFolder . $this->filename;
        $this->interval = date_interval_create_from_date_string($exportMessage->getInterval() . ' seconds');
        $this->generatedBy = $exportMessage->getUsername();
        return $this;
    }

    public function generate(): FileInfo
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->destinationFolder)) {
            $filesystem->mkdir($this->destinationFolder);
        }

        $filesystem->touch($this->filepath);
        $stopDate = (new \DateTime())->add($this->interval);
        $startDate = new \DateTime();

        while (new \DateTime() < $stopDate) {
            $now = \DateTime::createFromFormat('U.u', microtime(true));
            if ($now !== false) {
                $line = $now->format("Y-m-d H:i:s.u") . ';' . $this->randomFloatProvider->random(0, 1);
                $filesystem->appendToFile($this->filepath, $line . PHP_EOL);

                $this->notifier->send(new Notification(
                    ['user/' . $this->generatedBy . '/files'],
                    $this->getPercentageData($startDate, $stopDate),
                    true,
                    'progress-bar'
                ));
            }
        }

        $file = new File($this->filepath);

        return (new FileInfo())
            ->setFilename($this->getFilename())
            ->setFilepath($this->filepath)
            ->setFilesize(round($file->getSize() / 1024) . ' Ko')
            ->setGeneratedAt(time());
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    private function getPercentageData(\DateTime $startDate, \DateTime $stopDate): array
    {
        $percentage = (($stopDate->getTimestamp() -
                    (new \DateTime())->getTimestamp()) * 100) /
            ($stopDate->getTimestamp() - $startDate->getTimestamp());

        return ['percentage' => abs($percentage - 100)];
    }
}
