<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel;

class AjaxView extends View
{
    /** @var array */
    private $variables;

    /** @var array */
    private $errors;

    /** @var bool */
    private $showErrorsIfEmpty;

    /**
     * AjaxView constructor.
     */
    public function __construct()
    {
        $this->errors            = [];
        $this->variables         = [];
        $this->showErrorsIfEmpty = false;
    }

    /**
     * @return array
     */
    public function compile()
    {
        $returnData = $this->variables;

        if (!empty($this->errors) || $this->showErrorsIfEmpty) {
            $returnData['errors'] = $this->errors;
        }

        return $returnData;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addVariable($key, $value)
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * @param array $variables
     *
     * @return $this
     */
    public function addVariables(array $variables)
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function addError($message)
    {
        if ($message === null) {
            return $this;
        }

        if (is_array($message)) {
            $this->errors = array_merge($this->errors, $message);
        } else {
            $this->errors[] = $message;
        }

        return $this;
    }

    /**
     * @param array $messages
     *
     * @return $this
     */
    public function addErrors(array $messages)
    {
        foreach ($messages as $message) {
            $this->addError($message);
        }

        return $this;
    }

    /**
     * @param bool $showErrorsIfEmpty
     */
    public function setShowErrorsIfEmpty($showErrorsIfEmpty)
    {
        $this->showErrorsIfEmpty = (bool) $showErrorsIfEmpty;
    }
}
