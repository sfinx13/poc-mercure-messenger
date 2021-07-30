<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Uid\Uuid;

class Publisher
{
    protected HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function publish($topics, array $data, bool $private = false, string $type = null)
    {
        $update = new Update($topics, json_encode($data), $private, Uuid::v4(), $type);
        $this->hub->publish($update);
    }
}
