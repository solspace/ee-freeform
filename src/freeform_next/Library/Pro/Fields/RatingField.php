<?php

namespace Solspace\Addons\FreeformNext\Library\Pro\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\SingleValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Validation\Constraints\NumericConstraint;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;

class RatingField extends AbstractField implements SingleValueInterface
{
    const MIN_VALUE = 3;
    const MAX_VALUE = 10;

    use SingleValueTrait;

    /** @var int */
    protected $maxValue;

    /** @var string */
    protected $colorIdle;

    /** @var string */
    protected $colorHover;

    /** @var string */
    protected $colorSelected;

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::TYPE_RATING;
    }

    /**
     * @return int
     */
    public function getMaxValue()
    {
        $maxValue = (int) $this->maxValue;

        if ($maxValue < self::MIN_VALUE) {
            $maxValue = self::MIN_VALUE + 1;
        }

        if ($maxValue > self::MAX_VALUE) {
            $maxValue = self::MAX_VALUE;
        }

        return $maxValue;
    }

    /**
     * @return string
     */
    public function getColorIdle()
    {
        return $this->colorIdle;
    }

    /**
     * @return string
     */
    public function getColorHover()
    {
        return $this->colorHover;
    }

    /**
     * @return string
     */
    public function getColorSelected()
    {
        return $this->colorSelected;
    }

    /**
     * @inheritDoc
     */
    public function getConstraints()
    {
        return [
            new NumericConstraint(
                1,
                $this->getMaxValue(),
                null,
                null,
                null,
                false,
                null,
                null,
                null,
                $this->translate('Rating must be between {{min}} and {{max}}')
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getInputHtml()
    {
        $attributes = $this->getCustomAttributes();

        $output = $this->getStyles();

        $generatedClass = $this->getFormSha() . '-' . $this->getHandle() . '-rating-wrapper';

        $output .= '<div>';
        $output .= '<span class="'. $generatedClass . ' form-rating-field-wrapper"';
        $output .= $this->getAttributeString('id', $this->getIdAttribute());
        $output .= '>';

        $maxValue = $this->getMaxValue();
        for ($i = $maxValue; $i >= 1; $i--) {
            $starId = $this->getHandle() . '_star_' . $i;

            $output .= '<input';
            $output .= $this->getAttributeString('name', $this->getHandle());
            $output .= $this->getAttributeString('type', 'radio');
            $output .= $this->getAttributeString('id', $starId);
            $output .= $this->getAttributeString('class', $attributes->getClass());
            $output .= $this->getAttributeString('value', $i, false);
            if((int) $this->getValue() === $i)
			{
            	$output .= $this->getAttributeString('checked', true);
			}
            $output .= $attributes->getInputAttributesAsString();
            $output .= ' />' . PHP_EOL;

            $output .= '<label';
            $output .= $this->getAttributeString('for', $starId);
            $output .= '></label>';
        }
        $output .= '</span>';
        $output .= '<div style="clear: both;"></div>';
        $output .= '</div>';

        return $output;
    }

    /**
     * @return string
     */
    private function getStyles()
    {
        $cssPath = PATH_THIRD_THEMES . 'freeform_next/css/fields/rating.css';

        $output = '<style>' . PHP_EOL;
        $output .= @file_get_contents($cssPath);
        $output .= '</style>';

        $replaceMap = [
            'formhash' => $this->getFormSha(),
            'fieldname' => $this->getHandle(),
            'coloridle' => $this->getColorIdle(),
            'colorhover' => $this->getColorHover(),
            'colorselected' => $this->getColorSelected(),
        ];

        $output = str_replace(array_keys($replaceMap), $replaceMap, $output);

        return $output;
    }

    /**
     * @return string
     */
    private function getFormSha()
    {
        return 'f' . HashHelper::sha1($this->getForm()->getHash(), 6);
    }
}
