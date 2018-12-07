<?php

namespace Solspace\Addons\FreeformNext\Library\Factories;

use Solspace\Freeform\Freeform;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ExternalOptionsInterface;
use Solspace\Addons\FreeformNext\Library\Configuration\ExternalOptionsConfiguration;

class PredefinedOptionsFactory
{
    const TYPE_INT              = 'int';
    const TYPE_INT_LEADING_ZERO = 'int_w_zero';
    const TYPE_FULL             = 'full';
    const TYPE_ABBREVIATED      = 'abbreviated';

    /** @var ExternalOptionsConfiguration */
    private $configuration;

    /** @var array */
    private $selectedValues;

    /**
     * @param string                       $type
     * @param ExternalOptionsConfiguration $configuration
     * @param array                        $selectedValues
     *
     * @return Option[]
     */
    public static function create($type, ExternalOptionsConfiguration $configuration, array $selectedValues = [])
    {
        $instance = new self($configuration, $selectedValues);

        switch ($type) {
            case ExternalOptionsInterface::PREDEFINED_NUMBERS:
                $options = $instance->getNumberOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_YEARS:
                $options = $instance->getYearOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_MONTHS:
                $options = $instance->getMonthOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_DAYS:
                $options = $instance->getDayOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_DAYS_OF_WEEK:
                $options = $instance->getDaysOfWeekOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_COUNTRIES:
                $options = $instance->getCountryOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_LANGUAGES:
                $options = $instance->getLanguageOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_PROVINCES:
                $options = $instance->getProvinceOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_STATES:
                $options = $instance->getStateOptions();
                break;

            case ExternalOptionsInterface::PREDEFINED_STATES_TERRITORIES:
                $options = $instance->getStateTerritoryOptions();
                break;

            default:
                $options = [];
                break;
        }

        if ($configuration->getEmptyOption()) {
            array_unshift($options, new Option(lang($configuration->getEmptyOption()), ''));
        }

        return $options;
    }

    /**
     * PredefinedOptionsFactory constructor.
     *
     * @param ExternalOptionsConfiguration $configuration
     * @param array                        $selectedValues
     */
    private function __construct(ExternalOptionsConfiguration $configuration, array $selectedValues)
    {
        $this->configuration  = $configuration;
        $this->selectedValues = $selectedValues;
    }

    /**
     * @return Option[]
     */
    private function getNumberOptions()
    {
        $options = [];

        $start = $this->getConfig()->getStart() ?: 0;
        $end   = $this->getConfig()->getEnd() ?: 20;
        foreach (range($start, $end) as $number) {
            $options[] = new Option($number, $number, $this->isChecked($number));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getYearOptions()
    {
        $options = [];

        $currentYear = (int) date('Y');
        $start       = $this->getConfig()->getStart() ?: 100;
        $end         = $this->getConfig()->getEnd() ?: 0;
        $isDesc      = $this->getConfig()->getSort() === 'desc';

        $range = $isDesc ? range($currentYear + $end, $currentYear - $start) : range($currentYear - $start, $currentYear + $end);
        foreach ($range as $year) {
            $options[] = new Option($year, $year, $this->isChecked($year));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getMonthOptions()
    {
        $options = [];

        $labelFormat = self::getMonthFormatFromType($this->getConfig()->getListType());
        $valueFormat = self::getMonthFormatFromType($this->getConfig()->getValueType());
        foreach (range(0, 11) as $month) {
            $label = date($labelFormat, strtotime("january 2017 +$month month"));
            $value = date($valueFormat, strtotime("january 2017 +$month month"));

            $options[] = new Option($label, $value, $this->isChecked($value));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getDayOptions()
    {
        $options = [];

        $labelFormat = self::getDayFormatFromType($this->getConfig()->getListType());
        $valueFormat = self::getDayFormatFromType($this->getConfig()->getValueType());

        foreach (range(1, 31) as $dayIndex) {
            $label = $labelFormat === 'd' ? str_pad($dayIndex, 2, '0', STR_PAD_LEFT) : $dayIndex;
            $value = $valueFormat === 'd' ? str_pad($dayIndex, 2, '0', STR_PAD_LEFT) : $dayIndex;

            $options[] = new Option($label, $value, $this->isChecked($value));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getDaysOfWeekOptions()
    {
        $options = [];

        $firstDayOfWeek = $this->getConfig()->getStart() ?: 1;
        $labelFormat    = self::getDayOfTheWeekFormatFromType($this->getConfig()->getListType());
        $valueFormat    = self::getDayOfTheWeekFormatFromType($this->getConfig()->getValueType());
        foreach (range(0, 6) as $dayIndex) {
            $dayIndex += $firstDayOfWeek;

            $label = date($labelFormat, strtotime("Sunday +$dayIndex days"));
            $value = date($valueFormat, strtotime("Sunday +$dayIndex days"));

            $options[] = new Option($label, $value, $this->isChecked($value));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getCountryOptions()
    {
        /** @var array $countries */
        static $countries;
        if (null === $countries) {
            $countries = json_decode(file_get_contents(__DIR__ . '/Data/countries.json'), true);
        }

        $options      = [];
        $isShortLabel = $this->getConfig()->getListType() === self::TYPE_ABBREVIATED;
        $isShortValue = $this->getConfig()->getValueType() === self::TYPE_ABBREVIATED;
        foreach ($countries as $abbreviation => $countryName) {
            $label = $isShortLabel ? $abbreviation : $countryName;
            $value = $isShortValue ? $abbreviation : $countryName;

            $options[] = new Option($label, $value, $this->isChecked($value));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getLanguageOptions()
    {
        /** @var array $languages */
        static $languages;
        if (null === $languages) {
            $languages = json_decode(file_get_contents(__DIR__ . '/Data/languages.json'), true);
        }

        $options      = [];
        $isShortLabel = $this->getConfig()->getListType() === self::TYPE_ABBREVIATED;
        $isShortValue = $this->getConfig()->getValueType() === self::TYPE_ABBREVIATED;
        foreach ($languages as $abbreviation => $data) {
            $label = $isShortLabel ? $abbreviation : $data['name'];
            $value = $isShortValue ? $abbreviation : $data['name'];

            $options[] = new Option($label, $value, $this->isChecked($value));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getProvinceOptions()
    {
        /** @var array $provinces */
        static $provinces;
        if (null === $provinces) {
            $provinces = json_decode(file_get_contents(__DIR__ . '/Data/provinces.json'), true);
        }

        $options      = [];
        $isShortLabel = $this->getConfig()->getListType() === self::TYPE_ABBREVIATED;
        $isShortValue = $this->getConfig()->getValueType() === self::TYPE_ABBREVIATED;
        foreach ($provinces as $abbreviation => $provinceName) {
            $label = $isShortLabel ? $abbreviation : $provinceName;
            $value = $isShortValue ? $abbreviation : $provinceName;

            $options[] = new Option($label, $value, $this->isChecked($value));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getStateOptions()
    {
        /** @var array $states */
        static $states;
        if (null === $states) {
            $states = json_decode(file_get_contents(__DIR__ . '/Data/states.json'), true);
        }

        $options      = [];
        $isShortLabel = $this->getConfig()->getListType() === self::TYPE_ABBREVIATED;
        $isShortValue = $this->getConfig()->getValueType() === self::TYPE_ABBREVIATED;
        foreach ($states as $abbreviation => $stateName) {
            $label = $isShortLabel ? $abbreviation : $stateName;
            $value = $isShortValue ? $abbreviation : $stateName;

            $options[] = new Option($label, $value, $this->isChecked($value));
        }

        return $options;
    }

    /**
     * @return Option[]
     */
    private function getStateTerritoryOptions()
    {
        /** @var array $states */
        static $states;
        if (null === $states) {
            $states = json_decode(file_get_contents(__DIR__ . '/Data/states-territories.json'), true);
        }

        $options      = [];
        $isShortLabel = $this->getConfig()->getListType() === self::TYPE_ABBREVIATED;
        $isShortValue = $this->getConfig()->getValueType() === self::TYPE_ABBREVIATED;
        foreach ($states as $abbreviation => $stateName) {
            $label = $isShortLabel ? $abbreviation : $stateName;
            $value = $isShortValue ? $abbreviation : $stateName;

            $options[] = new Option($label, $value, $this->isChecked($value));
        }

        return $options;
    }


    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isChecked($value)
    {
        return \in_array($value, $this->selectedValues, true);
    }

    /**
     * @return ExternalOptionsConfiguration
     */
    private function getConfig()
    {
        return $this->configuration;
    }

    /**
     * @param string|null $type
     *
     * @return string
     */
    private static function getMonthFormatFromType($type = null)
    {
        $format = 'F';
        switch ($type) {
            case self::TYPE_INT:
                $format = 'n';
                break;

            case self::TYPE_INT_LEADING_ZERO:
                $format = 'm';
                break;

            case self::TYPE_ABBREVIATED:
                $format = 'M';
                break;
        }

        return $format;
    }

    /**
     * @param string|null $type
     *
     * @return string
     */
    private static function getDayFormatFromType($type = null)
    {
        $format = 'd';
        switch ($type) {
            case self::TYPE_INT:
            case self::TYPE_ABBREVIATED:
                $format = 'j';
                break;
        }

        return $format;
    }

    /**
     * @param string|null $type
     *
     * @return string
     */
    private static function getDayOfTheWeekFormatFromType($type = null)
    {
        $format = 'l';
        switch ($type) {
            case self::TYPE_INT:
                $format = 'N';
                break;

            case self::TYPE_ABBREVIATED:
                $format = 'D';
                break;
        }

        return $format;
    }
}
