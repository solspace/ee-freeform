<?php

namespace Solspace\Freeform\Library\Composer\Components\Validation\Constraints;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Validation\Constraints\ConstraintInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Validation\Errors\ConstraintViolationList;

class PhoneConstraint implements ConstraintInterface
{
    /** @var string */
    private $message;

    /**
     * The pattern is going to look like this:
     * (xxx) xxxx xxx
     * Anything other than an X is going to be assumed literal
     * an X stands for any digit between 0 and 9
     *
     * @var string
     */
    private $pattern;

    /** @var string */
    private $defaultCountry;

    /**
     * RegexConstraint constructor.
     *
     * @param string $message
     * @param string $pattern
     * @param string $defaultCountry
     */
    public function __construct($message = 'Invalid phone number', $pattern = null, $defaultCountry = 'US')
    {
        $this->message = $message;
        $this->pattern = !empty($pattern) ? $pattern : null;
        $this->defaultCountry = $defaultCountry;
    }

    /**
     * @inheritDoc
     */
    public function validate($value)
    {
        $violationList = new ConstraintViolationList();
        $pattern       = $this->pattern;

        if (null !== $pattern) {
            $compiledPattern = $pattern;
            $compiledPattern = preg_replace('/([\[\](){}$+_\-+])/', '\\\\$1', $compiledPattern);
            preg_match_all('/(x+)/i', $compiledPattern, $matches);

            if (isset($matches[1])) {
                foreach ($matches[1] as $match) {
                    $compiledPattern = preg_replace(
                        '/' . $match . '/',
                        '[0-9]{' . strlen($match) . '}',
                        $compiledPattern,
                        1
                    );
                }
            }

            $compiledPattern = '/^' . $compiledPattern . '$/';

            if (!preg_match($compiledPattern, $value)) {
                $violationList->addError($this->message);
            }

            return $violationList;
        }

        static $phoneUtil;

        if (null === $phoneUtil) {
            $phoneUtil = PhoneNumberUtil::getInstance();
        }

        try {
            $phoneNumber = $phoneUtil->parse($value, $this->defaultCountry);

            if (!$phoneUtil->isValidNumber($phoneNumber)) {
                $violationList->addError($this->message);
            }
        } catch (NumberParseException $e) {
            $violationList->addError($this->message);
        }

        return $violationList;
    }
}
