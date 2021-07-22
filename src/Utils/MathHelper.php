<?php

namespace App\Utils;

class MathHelper
{
    public static function randomFloat(int $min = 0, int $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
