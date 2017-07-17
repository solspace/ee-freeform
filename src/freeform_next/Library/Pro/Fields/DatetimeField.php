<?php

namespace Solspace\Addons\FreeformNext\Library\Pro\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\InitialValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\TextField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\InitialValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Validation\Constraints\DateTimeConstraint;

class DatetimeField extends TextField implements InitialValueInterface
{
    const DATETIME_TYPE_BOTH = 'both';
    const DATETIME_TYPE_DATE = 'date';
    const DATETIME_TYPE_TIME = 'time';

    use InitialValueTrait;

    /** @var string */
    protected $dateTimeType;

    /** @var bool */
    protected $generatePlaceholder;

    /** @var string */
    protected $dateOrder;

    /** @var bool */
    protected $date4DigitYear;

    /** @var bool */
    protected $dateLeadingZero;

    /** @var string */
    protected $dateSeparator;

    /** @var bool */
    protected $clock24h;

    /** @var bool */
    protected $lowercaseAMPM;

    /** @var string */
    protected $clockSeparator;

    /** @var string */
    protected $clockAMPMSeparate;

    /** @var bool */
    protected $useDatepicker;

    /**
     * @return string
     */
    public static function getFieldTypeName()
    {
        return 'Date & Time';
    }

    /**
     * @return string
     */
    public function getDateTimeType()
    {
        return $this->dateTimeType;
    }

    /**
     * @return bool
     */
    public function isGeneratePlaceholder()
    {
        return $this->generatePlaceholder;
    }

    /**
     * @return string
     */
    public function getDateOrder()
    {
        return $this->dateOrder;
    }

    /**
     * @return bool
     */
    public function isDate4DigitYear()
    {
        return $this->date4DigitYear;
    }

    /**
     * @return bool
     */
    public function isDateLeadingZero()
    {
        return $this->dateLeadingZero;
    }

    /**
     * @return string
     */
    public function getDateSeparator()
    {
        return $this->dateSeparator;
    }

    /**
     * @return bool
     */
    public function isClock24h()
    {
        return $this->clock24h;
    }

    /**
     * @return bool
     */
    public function isLowercaseAMPM()
    {
        return $this->lowercaseAMPM;
    }

    /**
     * @return string
     */
    public function getClockSeparator()
    {
        return $this->clockSeparator;
    }

    /**
     * @return bool
     */
    public function isClockAMPMSeparate()
    {
        return $this->clockAMPMSeparate;
    }

    /**
     * @return bool
     */
    public function isUseDatepicker()
    {
        return $this->useDatepicker;
    }

    /**
     * @return string|null
     */
    public function getPlaceholder()
    {
        if (!$this->isGeneratePlaceholder()) {
            return $this->placeholder;
        }

        return $this->getHumanReadableFormat();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $value = $this->value;

        if ($this->getValueOverride()) {
            $value = $this->getValueOverride();
        }

        if (empty($value)) {
            $value = $this->getInitialValue();

            if ($value) {
                try {
                    if (stripos(strtolower($value), 'today') !== false) {
                        $date = new \DateTime($value);
                    } else {
                        $date = new \DateTime($value);
                    }

                    return $date->format($this->getFormat());
                } catch (\Exception $e) {
                }
            }
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getConstraints()
    {
        return [
            new DateTimeConstraint(
                $this->translate(
                    '"{value}" does not conform to "{format}" format.',
                    [
                        'value'  => $this->getValue(),
                        'format' => $this->getHumanReadableFormat(),
                    ]
                ),
                $this->getFormat()
            ),
        ];
    }

    /**
     * @return string
     */
    public function getInputHtml()
    {
        $attributes = $this->getCustomAttributes();
        $this->addInputClass('form-date-time-field');

        if ($this->isUseDatepicker()) {
            $this->addInputClass('form-datepicker');
        }

        $hasTime = in_array($this->getDateTimeType(), [self::DATETIME_TYPE_BOTH, self::DATETIME_TYPE_TIME], true);
        $hasDate = in_array($this->getDateTimeType(), [self::DATETIME_TYPE_BOTH, self::DATETIME_TYPE_DATE], true);

        $classString = $attributes->getClass() . ' ' . $this->getInputClassString();

        return '<input '
            . $this->getAttributeString('name', $this->getHandle())
            . $this->getAttributeString('type', $this->getType())
            . $this->getAttributeString('id', $this->getIdAttribute())
            . $this->getAttributeString('class', $classString)
            . $this->getAttributeString('data-datepicker-format', $this->getDatepickerFormat())
            . $this->getAttributeString('data-datepicker-enableTime', $hasTime)
            . $this->getAttributeString('data-datepicker-enableDate', $hasDate)
            . $this->getAttributeString('data-datepicker-clock_24h', $this->isClock24h())
            . $this->getAttributeString(
                'placeholder',
                $this->translate($attributes->getPlaceholder() ?: $this->getPlaceholder())
            )
            . $this->getAttributeString('value', $this->getValue(), false)
            . $this->getRequiredAttribute()
            . $attributes->getInputAttributesAsString()
            . '/>';
    }

    /**
     * @return string
     */
    private function getDatepickerFormat()
    {
        $format = $this->getFormat();

        $datepickerFormat = str_replace(
            ['G', 'g', 'a', 'A'],
            ['H', 'h', 'K', 'K'],
            $format
        );

        return $datepickerFormat;
    }

    /**
     * Converts Y/m/d to YYYY/MM/DD, etc
     *
     * @return string
     */
    private function getHumanReadableFormat()
    {
        $format = $this->getFormat();

        $humanReadable = str_replace(
            ['Y','y','n','m','j','d','H','h','G','g','i','A','a'],
            ['YYYY','YY','M','MM','D','DD','HH','H','HH','H','MM','TT','TT'],
            $format
        );

        return $humanReadable;
    }

    /**
     * Gets the datetime format based on selected field settings
     *
     * @return string
     */
    private function getFormat()
    {
        $showDate = in_array($this->getDateTimeType(), [self::DATETIME_TYPE_BOTH, self::DATETIME_TYPE_DATE], true);
        $showTime = in_array($this->getDateTimeType(), [self::DATETIME_TYPE_BOTH, self::DATETIME_TYPE_TIME], true);

        $formatParts = [];
        if ($showDate) {
            $month = $this->isDateLeadingZero() ? 'm' : 'n';
            $day   = $this->isDateLeadingZero() ? 'd' : 'j';
            $year  = $this->isDate4DigitYear() ? 'Y' : 'y';

            $first = $second = $third = null;

            switch ($this->getDateOrder()) {
                case 'mdy':
                    $first  = $month;
                    $second = $day;
                    $third  = $year;

                    break;

                case 'dmy':
                    $first  = $day;
                    $second = $month;
                    $third  = $year;

                    break;

                case 'ymd':
                    $first  = $year;
                    $second = $month;
                    $third  = $day;

                    break;
            }

            $formatParts[] = sprintf(
                '%s%s%s%s%s',
                $first,
                $this->getDateSeparator(),
                $second,
                $this->getDateSeparator(),
                $third
            );
        }

        if ($showTime) {
            $minutes = 'i';

            if ($this->isClock24h()) {
                $hours = 'H';
                $ampm  = '';
            } else {
                $hours = 'g';
                $ampm  = ($this->isClockAMPMSeparate() ? ' ' : '') . ($this->isLowercaseAMPM() ? 'a' : 'A');
            }

            $formatParts[] = $hours . $this->getClockSeparator() . $minutes . $ampm;
        }

        return implode(' ', $formatParts);
    }
}
