<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Helpers;

use Hashids\Hashids;

class HashHelper
{
    const SALT       = "composer";
    const MIN_LENGTH = 9;

    /** @var Hashids */
    private static $hashids;

    /**
     * @param int $id
     *
     * @return string
     */
    public static function hash($id)
    {
        return self::getHashids()->encode($id);
    }

    /**
     * @param string $hash
     *
     * @return int
     */
    public static function decode($hash)
    {
        $idList = self::getHashids()->decode($hash);

        return array_pop($idList);
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    public static function decodeMultiple($hash)
    {
        return self::getHashids()->decode($hash);
    }

    /**
     * @param mixed $value
     * @param int   $length
     * @param int   $offset
     *
     * @return string
     */
    public static function sha1($value, $length = null, $offset = 0)
    {
        $hash = sha1($value);

        if ($length) {
            return substr($hash, $offset, $length);
        }

        return $hash;
    }

    /**
     * @return Hashids
     */
    private static function getHashids()
    {
        if (is_null(self::$hashids)) {
            self::$hashids = new Hashids(self::SALT, self::MIN_LENGTH);
        }

        return self::$hashids;
    }
}
