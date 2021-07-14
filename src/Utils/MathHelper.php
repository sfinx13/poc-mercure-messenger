<?php


namespace App\Utils;


class MathHelper
{
    /**
     * @param int $min
     * @param int $max
     * @return float|int
     */
    public static function randomFloat($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
