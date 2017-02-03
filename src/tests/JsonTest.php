<?php

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
