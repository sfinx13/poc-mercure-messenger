<?php

namespace App\Message;


class ExportMessage
{

    /**
     * @var int
     */
    protected $projectId;

    /**
     * @var int
     */
    protected $interval;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     * @return ExportMessage
     */
    public function setProjectId(int $projectId): ExportMessage
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @return int
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * @param int $interval
     * @return ExportMessage
     */
    public function setInterval(int $interval): ExportMessage
    {
        $this->interval = $interval;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return ExportMessage
     */
    public function setStartDate(\DateTime $startDate): ExportMessage
    {
        $this->startDate = $startDate;
        return $this;
    }

}
