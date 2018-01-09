<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Helpers;

use PHPUnit\Framework\TestCase;
use Solspace\Freeform\Library\Helpers\HashHelper;

class HashHelperTest extends TestCase
{
    public function sha1DataProvider()
    {
        return [
            [null, 0, "a9993e364706816aba3e25717850c26c9cd0d89d"],
            [1, 0, "a"],
            [10, 0, "a9993e3647"],
            [10, 10, "06816aba3e"],
        ];
    }

    /**
     * @param int    $length
     * @param int    $offset
     * @param string $expectedOutput
     *
     * @dataProvider sha1DataProvider
     */
    public function testSha1($length, $offset, $expectedOutput)
    {
        $hashed = HashHelper::sha1("abc", $length, $offset);

        $this->assertSame($expectedOutput, $hashed);
    }
}
