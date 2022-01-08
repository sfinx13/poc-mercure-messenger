<?php

namespace App\Service\Generator;

use App\Model\FileInfo;

interface FileGeneratorInterface
{
    public function generate(): FileInfo;

    public function getFilename(): string;
}
