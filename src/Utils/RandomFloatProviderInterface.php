<?php

namespace App\Utils;

interface RandomFloatProviderInterface
{
    public function random(int $min = 0, int $max = 1): float;
}
