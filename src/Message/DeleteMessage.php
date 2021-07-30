<?php

namespace App\Message;

use App\Entity\User;

class DeleteMessage
{
    protected User $user;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): DeleteMessage
    {
        $this->user = $user;
        return $this;
    }
}
