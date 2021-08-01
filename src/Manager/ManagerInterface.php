<?php

namespace App\Manager;

interface ManagerInterface
{
    public function save($entity, bool $flush = true);
    public function remove($entity, bool $flush = true);
    public function flush();
}
