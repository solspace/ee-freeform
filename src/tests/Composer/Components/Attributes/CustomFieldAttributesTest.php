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

namespace Composer\Components\Attributes;

use Solspace\Freeform\Library\Composer\Components\Attributes\CustomFieldAttributes;
use Solspace\Freeform\Library\Composer\Components\Attributes\CustomFormAttributes;

class CustomFieldAttributesTest extends \PHPUnit_Framework_TestCase
{
    private $fieldMock;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->fieldMock = $this
            ->getMockBuilder('Solspace\Freeform\Library\Composer\Components\Fields\TextField')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testClass()
    {
        $attributes = new CustomFieldAttributes($this->fieldMock, ["class" => ["some", "class"]]);

        $this->assertSame("some class", $attributes->getClass());
    }

    public function testLabelClass()
    {
        $attributes = new CustomFieldAttributes($this->fieldMock, ["labelClass" => "some class"]);

        $this->assertSame("some class", $attributes->getLabelClass());
    }

    public function testErrorClass()
    {
        $attributes = new CustomFieldAttributes($this->fieldMock, ["errorClass" => "some class"]);

        $this->assertSame("some class", $attributes->getErrorClass());
    }

    public function testId()
    {
        $attributes = new CustomFieldAttributes($this->fieldMock, ["id" => "someId"]);

        $this->assertSame("someId", $attributes->getId());
    }

    public function testPlaceholder()
    {
        $attributes = new CustomFieldAttributes($this->fieldMock, ["placeholder" => "a placeholder"]);

        $this->assertSame("a placeholder", $attributes->getPlaceholder());
    }

    public function testDefaultValue()
    {
        $attributes = new CustomFieldAttributes($this->fieldMock, ["overrideValue" => "a default value"]);

        $this->assertSame("a default value", $attributes->getOverrideValue());
    }

    public function testUseRequiredAttribute()
    {
        $attributes = new CustomFieldAttributes($this->fieldMock, ["useRequiredAttribute" => true]);

        $this->assertSame(true, $attributes->getUseRequiredAttribute());
    }

    public function testInputAttributes()
    {
        $attributes = new CustomFieldAttributes(
            $this->fieldMock,
            [
                "inputAttributes" => [
                    "data-type"       => "test-type",
                    "novalidate"      => true,
                    "noshowattribute" => false,
                ],
            ]
        );

        $this->assertSame(
            [
                "data-type"       => "test-type",
                "novalidate"      => true,
                "noshowattribute" => false,
            ],
            $attributes->getInputAttributes()
        );
    }

    public function testInputAttributesAsString()
    {
        $attributes = new CustomFieldAttributes(
            $this->fieldMock,
            [
                "inputAttributes" => [
                    "data-type"       => "test-type",
                    "novalidate"      => true,
                    "noshowattribute" => false,
                ],
            ]
        );

        $expectedOutput = ' data-type="test-type" novalidate ';

        $this->assertSame($expectedOutput, $attributes->getInputAttributesAsString());
    }

    public function testCombineAttributes()
    {
        $this->fieldMock
            ->method("getHandle")
            ->willReturn("mockField");

        $formAttributes = new CustomFormAttributes(
            [
                "id"                   => "formId",
                "inputClass"           => "input class  list",
                "labelClass"           => " label classes",
                "errorClass"           => "form error class",
                "useRequiredAttribute" => false,
                "inputAttributes"      => [
                    "data-type"       => "test-type",
                    "unchanged"       => "value",
                    "novalidate"      => true,
                    "noshowattribute" => false,
                ],
                "overrideValues"       => [
                    "mockField" => "overriden default value",
                ],
            ]
        );
        $attributes     = new CustomFieldAttributes(
            $this->fieldMock,
            [
                "class"                => "initial   class",
                "labelClass"           => "field specific label class",
                "useRequiredAttribute" => true,
                "inputAttributes"      => [
                    "data-type"       => "overridden-type",
                    "noshowattribute" => true,
                    "newattribute"     => "new value",
                ],
            ],
            $formAttributes
        );

        $this->assertSame("initial class input list", $attributes->getClass());
        $this->assertSame("field specific label class classes", $attributes->getLabelClass());
        $this->assertSame("form error class", $attributes->getErrorClass());
        $this->assertSame("overriden default value", $attributes->getOverrideValue());
        $this->assertSame(
            ' data-type="overridden-type" unchanged="value" novalidate noshowattribute newattribute="new value" ',
            $attributes->getInputAttributesAsString()
        );
        $this->assertSame(true, $attributes->getUseRequiredAttribute());
        $this->assertSame(null, $attributes->getId());
    }

    public function testNotOverridingSetValue()
    {
        $this->fieldMock
            ->method("getHandle")
            ->willReturn("mockField");

        $formAttributes = new CustomFormAttributes(
            [
                "overrideValues" => [
                    "mockField" => "overriden default value",
                ],
            ]
        );
        $attributes     = new CustomFieldAttributes(
            $this->fieldMock,
            [
                "overrideValue" => "test",
            ],
            $formAttributes
        );

        $this->assertSame("test", $attributes->getOverrideValue());
    }
}
