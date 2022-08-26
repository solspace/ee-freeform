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
