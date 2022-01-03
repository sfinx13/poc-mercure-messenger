<?php

namespace App\Manager;

use App\Entity\File;
use App\Entity\User;
use App\Factory\FileFactory;
use App\Message\ExportMessage;
use App\Model\FileInfo;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileManager extends AbstractManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected FileRepository $fileRepository,
        protected FileFactory $fileFactory,
        protected ParameterBagInterface $parameterBag
    ) {
        parent::__construct($entityManager);
    }

    public function createFrom(ExportMessage $exportMessage): File
    {
        $file = $this->fileFactory->createInstanceFrom($exportMessage);
        $this->save($file);

        return $file;
    }

    public function updateFrom(string $fileId, FileInfo $fileInfo)
    {
        $file = $this->fileRepository->find($fileId);
        $file
            ->setStatus(File::STATUS_READY)
            ->setSize($fileInfo->getFilesize())
            ->setExportedAt((new \DateTimeImmutable())->setTimestamp($fileInfo->getGeneratedAt()));
        $this->save($file);
    }

    public function findOneBy(array $criteria): ?File
    {
        return $this->fileRepository->findOneBy($criteria);
    }


    public function removeFromUser(User $user): void
    {
        $files = $this->fileRepository->findBy([
            'user' => $user
        ]);

        foreach ($files as $file) {
            $this->remove($file, false);
            $this->removeFromFilesystem($file->getFilepath());
        }
        $this->flush();
    }

    public function removeFromFilename(string $filename): void
    {
        $file = $this->fileRepository->findOneBy([
            'filename' => $filename
        ]);

        $this->removeFromFilesystem($file->getFilepath());
        $this->remove($file);
    }

    public function removeFromFilesystem($filepath): void
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($filepath)) {
            $filesystem->remove($filepath);
        }
    }
}
