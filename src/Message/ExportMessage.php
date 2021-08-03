<?php

namespace App\Message;

use Symfony\Component\Security\Core\User\UserInterface;

class ExportMessage implements MessageInterface
{
    private string $username;

    private string $filename;

    private string $template;

    private int $interval;

    private \DateTime $startDate;

    private array $data;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
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

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function setInterval(int $interval): self
    {
        $this->interval = $interval;
        return $this;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): ExportMessage
    {
        $this->data = $data;
        return $this;
    }
}
