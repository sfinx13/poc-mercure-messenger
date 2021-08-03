<?php

namespace App\Model;

class FileInfo
{
    private string $filename;

    private string $filesize;

    private string $generatedAt;

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getFilesize(): string
    {
        return $this->filesize;
    }

    public function setFilesize(string $filesize): self
    {
        $this->filesize = $filesize;
        return $this;
    }

    public function getGeneratedAt(): string
    {
        return $this->generatedAt;
    }

    public function setGeneratedAt(string $generatedAt): self
    {
        $this->generatedAt = $generatedAt;
        return $this;
    }
}
