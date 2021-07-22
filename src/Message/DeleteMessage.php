<?php

namespace App\Message;

class DeleteMessage
{
    protected string $extension;

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): DeleteMessage
    {
        $this->extension = $extension;
        return $this;
    }
}
