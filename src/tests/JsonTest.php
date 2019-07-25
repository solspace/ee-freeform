<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

class JsonTest extends \PHPUnit\Framework\TestCase
{
    public function testJsonEncodeBoolean()
    {
        $std = new stdClass();
        $std->value = true;

        $this->assertEquals(
            '{"value":true}',
            json_encode($std)
        );
    }
}
