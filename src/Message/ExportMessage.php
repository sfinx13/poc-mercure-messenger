<?php

namespace App\Message;

use App\Entity\User;

class ExportMessage implements MessageInterface
{
    protected User $user;

    protected string $filename;

    protected int $interval;

    protected \DateTime $startDate;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): ExportMessage
    {
        $this->user = $user;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): ExportMessage
    {
        $this->filename = $filename;
        return $this;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function setInterval(int $interval): ExportMessage
    {
        $this->interval = $interval;
        return $this;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): ExportMessage
    {
        $this->startDate = $startDate;
        return $this;
    }
}
