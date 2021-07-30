<?php

namespace App\Manager;

use App\Entity\File;
use App\Entity\User;
use App\Factory\FileFactory;
use App\Message\ExportMessage;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileManager
{
    protected EntityManagerInterface $entityManager;
    protected FileRepository $fileRepository;
    protected FileFactory $fileFactory;
    protected ParameterBagInterface $parameterBag;

    public function __construct(
        EntityManagerInterface $entityManager,
        FileRepository $fileRepository,
        FileFactory $fileFactory,
        ParameterBagInterface $parameterBag
    ) {
        $this->entityManager = $entityManager;
        $this->fileRepository = $fileRepository;
        $this->fileFactory = $fileFactory;
        $this->parameterBag = $parameterBag;
    }

    public function createNew(ExportMessage $exportMessage): File
    {
        $file = $this->fileFactory->createInstance($exportMessage);
        $this->save($file);

        return $file;
    }

    public function findOneBy(array $criteria): ?File
    {
        return $this->fileRepository->findOneBy($criteria);
    }

    public function save(File $file, bool $flush = true): void
    {
        $this->entityManager->persist($file);
        if ($flush) {
            $this->flush();
        }
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

    public function removeFromFilesystem($filepath): void
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($filepath)) {
            $filesystem->remove($filepath);
        }
    }

    public function remove(File $file, bool $flush = true): void
    {
        $this->entityManager->remove($file);
        if ($flush) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
