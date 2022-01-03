<?php

namespace App\Factory;

use App\Entity\File;
use App\Message\ExportMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

class FileFactory
{
    public function __construct(private ParameterBagInterface $parameterBag, private Security $security) {}

    public function createInstanceFrom(ExportMessage $exportMessage): File
    {
        $filepath = $this->parameterBag->get('kernel.project_dir')
            . $this->parameterBag->get('destination_folder')
            . $exportMessage->getFilename();

        return (new File())
            ->setUser($this->security->getUser())
            ->setFilename($exportMessage->getFilename())
            ->setFilepath($filepath)
            ->setStatus(File::STATUS_DRAFT);
    }
}
