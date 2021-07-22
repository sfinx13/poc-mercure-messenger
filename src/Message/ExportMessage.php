<?php

namespace App\Message;


class ExportMessage
{
    protected int $projectId;

    protected int $interval;

    protected \DateTime $startDate;

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function setProjectId(int $projectId): ExportMessage
    {
        $this->projectId = $projectId;
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
