<?php

namespace App\Message;

use Symfony\Component\Security\Core\User\UserInterface;

interface MessageInterface
{
    public function getFilename(): ?string;
    public function getUsername(): string;
    public function getTemplate(): ?string;
}
