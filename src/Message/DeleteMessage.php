<?php

namespace App\Message;

class DeleteMessage implements MessageInterface
{
    private string $username;

    private ?string $filename;

    private ?string $template;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename ?? null;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }
}
