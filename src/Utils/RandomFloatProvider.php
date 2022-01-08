<?php

namespace App\Utils;

class RandomFloatProvider implements RandomFloatProviderInterface
{
    public function random(int $min = 0, int $max = 1): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
