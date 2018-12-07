<?php

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces;

interface ExternalOptionsInterface extends OptionsInterface
{
    const SOURCE_CUSTOM     = 'custom';
    const SOURCE_ENTRIES    = 'entries';
    const SOURCE_CATEGORIES = 'categories';
    const SOURCE_MEMBERS    = 'members';
    const SOURCE_PREDEFINED = 'predefined';

    const PREDEFINED_DAYS               = 'days';
    const PREDEFINED_DAYS_OF_WEEK       = 'days_of_week';
    const PREDEFINED_MONTHS             = 'months';
    const PREDEFINED_NUMBERS            = 'numbers';
    const PREDEFINED_YEARS              = 'years';
    const PREDEFINED_PROVINCES          = 'provinces';
    const PREDEFINED_STATES             = 'states';
    const PREDEFINED_STATES_TERRITORIES = 'states_territories';
    const PREDEFINED_COUNTRIES          = 'countries';
    const PREDEFINED_LANGUAGES          = 'languages';

    /**
     * Returns the option source
     *
     * @return string
     */
    public function getOptionSource();

    /**
     * @return mixed
     */
    public function getOptionTarget();

    /**
     * @return array
     */
    public function getOptionConfiguration();
}
