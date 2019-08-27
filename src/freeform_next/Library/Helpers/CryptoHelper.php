<?php

namespace Solspace\Addons\FreeformNext\Library\Helpers;

class CryptoHelper
{
    /**
     * Generate a unique token
     *
     * @param int $length
     *
     * @return string
     */
    public static function getUniqueToken($length = 40)
    {
        $token        = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max          = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::getSecureRandomInt(0, $max - 1)];
        }

        return $token;
    }

    /**
     * Generate a secure random int
     *
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public static function getSecureRandomInt($min, $max)
    {
        if (function_exists('random_int')) {
            return random_int($min, $max);
        }

        $range = $max - $min;

        if ($range < 1) {
            return $min; // not so random...
        }

        $log    = ceil(log($range, 2));
        $bytes  = (int) ($log / 8) + 1; // length in bytes
        $bits   = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);

        return $min + $rnd;
    }
}
