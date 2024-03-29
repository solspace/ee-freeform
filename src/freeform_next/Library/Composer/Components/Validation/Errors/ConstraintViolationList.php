<?php

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Validation\Errors;

class ConstraintViolationList implements \Countable
{
    /** @var array */
    private $errors;

    /**
     * ValidationErrors constructor.
     */
    public function __construct()
    {
        $this->errors = [];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('; ', $this->errors);
    }

    /**
     * @param string $message
     */
    public function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->errors);
    }

    /**
     * @param ConstraintViolationList $list
     *
     * @return $this
     */
    public function merge(ConstraintViolationList $list)
    {
        foreach ($list->getErrors() as $error) {
            $this->addError($error);
        }

        return $this;
    }
}
