<?php

namespace App\Messenger\Message;

interface MessageInterface
{
    public function getFilename(): ?string;
    public function getUsername(): string;
    public function getTemplate(): ?string;
}
