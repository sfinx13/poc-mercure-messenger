<?php

namespace App\Message;

use App\Entity\User;

interface MessageInterface
{
    public function getFilename(): string;
    public function getUser(): User;
}