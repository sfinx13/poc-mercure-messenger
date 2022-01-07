<?php

namespace App\Service\Notification;

class Notification
{
    private string $content;

    private array $topics;

    private array $data;

    private bool $private;

    private ?string $eventType;

    public function __construct(array $topics, array $data, bool $private = true, string $eventType = null)
    {
        $this->topics = $topics;
        $data['created_at'] = (new \DateTime())->format('H:i:s');
        $this->data = $data;
        $this->private = $private;
        $this->eventType = $eventType;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getTopics(): array
    {
        return $this->topics;
    }

    public function setTopics(array $topics): self
    {
        $this->topics = $topics;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;
        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType): self
    {
        $this->eventType = $eventType;
        return $this;
    }
}
