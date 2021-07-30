<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ActivableTrait
{
    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private bool $active = true;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }
}
