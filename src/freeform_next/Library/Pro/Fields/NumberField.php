<?php

namespace Solspace\Addons\FreeformNext\Library\Pro\Fields;

use Solspace\Freeform\Library\Composer\Components\Fields\TextField;
use Solspace\Freeform\Library\Composer\Components\Validation\Constraints\LengthConstraint;
use Solspace\Freeform\Library\Composer\Components\Validation\Constraints\NumericConstraint;

class NumberField extends TextField
{
    /** @var int */
    protected $minLength;

    /** @var int */
    protected $maxLength;

    /** @var int */
    protected $minValue;

    /** @var int */
    protected $maxValue;

    /** @var int */
    protected $decimalCount;

    /** @var string */
    protected $decimalSeparator;

    /** @var string */
    protected $thousandsSeparator;

    /** @var bool */
    protected $allowNegative;

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::TYPE_NUMBER;
    }

    /**
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @return int
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * @return int
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @return int
     */
    public function getDecimalCount()
    {
        return $this->decimalCount;
    }

    /**
     * @return string
     */
    public function getDecimalSeparator()
    {
        return $this->decimalSeparator;
    }

    /**
     * @return string
     */
    public function getThousandsSeparator()
    {
        return $this->thousandsSeparator;
    }

    /**
     * @return bool
     */
    public function isAllowNegative()
    {
        return $this->allowNegative;
    }

    /**
     * @inheritDoc
     */
    public function getConstraints()
    {
        return [
            new NumericConstraint(
                $this->getMinValue(),
                $this->getMaxValue(),
                $this->getDecimalCount(),
                $this->getDecimalSeparator(),
                $this->getThousandsSeparator(),
                $this->isAllowNegative(),
                $this->translate('Value must be numeric'),
                $this->translate('The value must be no more than {{max}}'),
                $this->translate('The value must be no less than {{min}}'),
                $this->translate('The value must be between {{min}} and {{max}}'),
                $this->translate('{{dec}} decimal places allowed'),
                $this->translate('Only positive numbers allowed')
            ),
            new LengthConstraint(
                $this->getMinLength(),
                $this->getMaxLength(),
                $this->translate('The value must be no more than {{max}} characters'),
                $this->translate('The value must be no less than {{min}} characters'),
                $this->translate('The value must be between {{min}} and {{max}} characters')
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getInputHtml()
    {
        $output = parent::getInputHtml();
        $output = str_replace('/>', '', $output);

        $output .= '/>';

        return $output;
    }
}
