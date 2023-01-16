<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Helpers;

use PHPUnit\Framework\TestCase;
use Solspace\Addons\FreeformNext\Library\Helpers\StringHelper;

class StringHelperTest extends TestCase
{
    public function replaceValuesDataProvider()
    {
        return [
            ["{foo} bar", ["foo" => "baz"], "baz bar"],
            ["{foo} {foo} {bar}", ["foo" => "baz", "bar" => "foo"], "baz baz foo"],
            ["foo bar", ["foo" => "baz", "bar" => "foo"], "foo bar"],
            ["{CasE} {cASe}", ["CasE" => "sensitive", "cASe" => "very"], "sensitive very"],
            ["{NonReplace} test", [], "{NonReplace} test"],
        ];
    }

    /**
     * @param $string
     * @param $values
     * @param $expectedOutput
     *
     * @dataProvider replaceValuesDataProvider
     */
    public function testReplaceValues($string, $values, $expectedOutput)
    {
        $output = StringHelper::replaceValues($string, $values);

        $this->assertSame($expectedOutput, $output);
    }

    public function testFlattenValues()
    {
        $values = [
            "key"  => ["foo", "bar"],
            "solo" => "Han",
        ];

        $expectedValue = [
            "key" => "foo, bar",
            "solo" => "Han",
        ];

        $this->assertSame($expectedValue, StringHelper::flattenArrayValues($values));
    }

    public function humanizeDataProvider()
    {
        return [
            ["testString", "test string"],
            ["TestString", "test string"],
            ["test_string", "test string"],
            ["test string", "test string"],
        ];
    }

    /**
     * @param string $input
     * @param string $expectedOutput
     *
     * @dataProvider humanizeDataProvider
     */
    public function testHumanize($input, $expectedOutput)
    {
        $output = StringHelper::humanize($input);

        $this->assertSame($expectedOutput, $output);
    }

    public function camelizeDataPrivder()
    {
        return [
            ["test string", "Test String", " "],
            ["testString", "TestString", " "],
            ["test_string", "Test_String", "_"],
            ["Test string", "Test String", " "],
        ];
    }

    /**
     * @param string $input
     * @param string $expectedOutput
     * @param string $delimiter
     *
     * @dataProvider camelizeDataPrivder
     */
    public function testCamelize($input, $expectedOutput, $delimiter)
    {
        $output = StringHelper::camelize($input, $delimiter);

        $this->assertSame($expectedOutput, $output);
    }

    public function recursiveImplodeDataProvider()
    {
        return [
            ['|', ['a', 2, 'test'], 'a|2|test'],
            [', ', ['a', 2, ['test', 'asd']], 'a, 2, test, asd'],
            [', ', [['a', 'best'], 2, ['test', 'asd']], 'a, best, 2, test, asd'],
            [',', 'test', 'test'],
        ];
    }

    /**
     * @param string $glue
     * @param array  $input
     * @param string $expectedOutput
     *
     * @dataProvider recursiveImplodeDataProvider
     */
    public function testImplodeRecursively($glue, $input, $expectedOutput)
    {
        $output = StringHelper::implodeRecursively($glue, $input);

        $this->assertSame($expectedOutput, $output);
    }
}
