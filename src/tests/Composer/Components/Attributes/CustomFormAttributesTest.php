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

use Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\CustomFormAttributes;

class CustomFormAttributesTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $attributes = new CustomFormAttributes(["class" => ["some", "class"]]);

        $this->assertSame("some class", $attributes->getClass());
    }

    public function testInputClass()
    {
        $attributes = new CustomFormAttributes(["inputClass" => "some class"]);

        $this->assertSame("some class", $attributes->getInputClass());
    }

    public function testLabelClass()
    {
        $attributes = new CustomFormAttributes(["labelClass" => "some class"]);

        $this->assertSame("some class", $attributes->getLabelClass());
    }

    public function testErrorClass()
    {
        $attributes = new CustomFormAttributes(["errorClass" => "some class"]);

        $this->assertSame("some class", $attributes->getErrorClass());
    }

    public function testSubmitClass()
    {
        $attributes = new CustomFormAttributes(["submitClass" => "some class"]);

        $this->assertSame("some class", $attributes->getSubmitClass());
    }

    public function testRowClass()
    {
        $attributes = new CustomFormAttributes(["rowClass" => "some class"]);

        $this->assertSame("some class", $attributes->getRowClass());
    }

    public function testColumnClass()
    {
        $attributes = new CustomFormAttributes(["columnClass" => "some class"]);

        $this->assertSame("some class", $attributes->getColumnClass());
    }

    public function testId()
    {
        $attributes = new CustomFormAttributes(["id" => "someId"]);

        $this->assertSame("someId", $attributes->getId());
    }

    public function testName()
    {
        $attributes = new CustomFormAttributes(["name" => "someName"]);

        $this->assertSame("someName", $attributes->getName());
    }

    public function testMethod()
    {
        $attributes = new CustomFormAttributes(["method" => "post"]);

        $this->assertSame("post", $attributes->getMethod());
    }

    public function testAction()
    {
        $attributes = new CustomFormAttributes(["action" => "http://google.com/"]);

        $this->assertSame("http://google.com/", $attributes->getAction());
    }

    public function testUseRequiredAttribute()
    {
        $attributes = new CustomFormAttributes(["useRequiredAttribute" => true]);

        $this->assertSame(true, $attributes->getUseRequiredAttribute());
    }

    public function testFormAttributes()
    {
        $attributes = new CustomFormAttributes(
            [
                "formAttributes" => [
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
            $attributes->getFormAttributes()
        );
    }

    public function testFormAttributesAsString()
    {
        $attributes = new CustomFormAttributes(
            [
                "formAttributes" => [
                    "data-type"       => "test-type",
                    "novalidate"      => true,
                    "noshowattribute" => false,
                ],
            ]
        );

        $expectedOutput = ' data-type="test-type" novalidate ';

        $this->assertSame($expectedOutput, $attributes->getFormAttributesAsString());
    }

    public function testInputAttributes()
    {
        $attributes = new CustomFormAttributes(
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

    public function testMerge()
    {
        $attributes = new CustomFormAttributes(
            [
                "id"             => "testID",
                "overrideValues" => ["mockField" => "test"],
                "action"         => "http://google.com/",
            ]
        );

        $this->assertSame("http://google.com/", $attributes->getAction());
        $this->assertSame("testID", $attributes->getId());
        $this->assertSame(["mockField" => "test"], $attributes->getOverrideValues());

        $attributes->mergeAttributes(
            [
                "id" => null,
                "action" => "different-action",
            ]
        );

        $this->assertSame("different-action", $attributes->getAction());
        $this->assertSame(null, $attributes->getId());
        $this->assertSame(["mockField" => "test"], $attributes->getOverrideValues());
    }
}
