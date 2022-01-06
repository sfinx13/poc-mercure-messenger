<?php

namespace App\Tests\Model;

use App\Model\FileInfo;
use PHPUnit\Framework\TestCase;

class FileInfoTest extends TestCase
{
    public function testModelIsTrue(): void
    {
        $generatedAt = time();
        $fileInfo = (new FileInfo())
            ->setFilename('dummy.csv')
            ->setFilesize('50 Ko')
            ->setGeneratedAt($generatedAt);

        $this->assertTrue($fileInfo->getFilename() === 'dummy.csv');
        $this->assertTrue($fileInfo->getFilesize() === '50 Ko');
        $this->assertTrue($fileInfo->getGeneratedAt() === $generatedAt);

    }

    public function testModelIsFalse(): void
    {
        $generatedAt = time()  + 1 ;
        $fileInfo = (new FileInfo())
            ->setFilename('dummies.csv')
            ->setFilesize('1024 Ko')
            ->setGeneratedAt($generatedAt);

        $this->assertFalse($fileInfo->getFilename() === 'dummy.csv');
        $this->assertFalse($fileInfo->getFilesize() === '50 Ko');
        $this->assertFalse($fileInfo->getGeneratedAt() === time());
    }

    public function testModelIsEmpty()
    {
        $fileInfo = new FileInfo();

        $this->assertEmpty($fileInfo->getFilename());
        $this->assertEmpty($fileInfo->getFilesize());
        $this->assertEmpty($fileInfo->getGeneratedAt());
    }
}
