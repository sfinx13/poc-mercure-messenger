<?php

namespace App\Factory;

use App\Entity\File;
use App\Message\ExportMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

class FileFactory
{
    protected Security $security;
    protected ParameterBagInterface $parameterBag;


    public function __construct(Security $security, ParameterBagInterface $parameterBag)
    {
        $this->security = $security;
        $this->parameterBag = $parameterBag;
    }

    public function createInstance(ExportMessage $exportMessage): File
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
