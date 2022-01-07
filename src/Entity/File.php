<?php

namespace App\Entity;

use App\Repository\FileRepository;
use App\Traits\ActivableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class File
{
    use TimestampableTrait;
    use ActivableTrait;

    public const STATUS_DRAFT = 0;
    public const STATUS_READY = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $filename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $filepath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $size;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $exportedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $downloadCount = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): File
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /**
     * @param string $filepath
     * @return File
     */
    public function setFilepath(string $filepath): File
    {
        $this->filepath = $filepath;
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getExportedAt(): ?\DateTimeImmutable
    {
        return $this->exportedAt;
    }

    public function setExportedAt(?\DateTimeImmutable $exportedAt): self
    {
        $this->exportedAt = $exportedAt;

        return $this;
    }

    public function getDownloadCount(): int
    {
        return $this->downloadCount;
    }

    public function setDownloadCount(int $downloadCount): self
    {
        $this->downloadCount = $downloadCount;

        return $this;
    }

    public function getStatus(): ?int

    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
