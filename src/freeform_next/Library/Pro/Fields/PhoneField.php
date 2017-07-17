<?php

namespace Solspace\Addons\FreeformNext\Library\Pro\Fields;

use Solspace\Freeform\Library\Composer\Components\Fields\TextField;
use Solspace\Freeform\Library\Composer\Components\Validation\Constraints\PhoneConstraint;

class PhoneField extends TextField
{
    /** @var string */
    protected $pattern;

    /** @var string */
    protected $countryCode;

    /**
     * @return string|null
     */
    public function getPattern()
    {
        return !empty($this->pattern) ? $this->pattern : null;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @inheritDoc
     */
    public function getConstraints()
    {
        return [
            new PhoneConstraint(
                $this->translate('Invalid phone number'),
                $this->getPattern(),
                $this->getCountryCode()
            ),
        ];
    }
}
