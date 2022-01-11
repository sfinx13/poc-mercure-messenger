<?php

namespace App\Service\Notification;

interface NotifierInterface
{
    public function send(Notification $notification);
}
