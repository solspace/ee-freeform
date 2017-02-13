<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

/**
 * Created by PhpStorm.
 * User: gustavs
 * Date: 17.13.2
 * Time: 11:24
 */
namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel;

class AjaxView extends View
{
    /** @var array */
    private $variables;

    /** @var array */
    private $errors;

    /**
     * AjaxView constructor.
     */
    public function __construct()
    {
        $this->errors    = [];
        $this->variables = [];
    }

    /**
     * @return array
     */
    public function compile()
    {
        $returnData = [];
        if ($this->hasErrors()) {
            $returnData['errors'] = $this->errors;
        }

        $returnData = array_merge($returnData, $this->variables);

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
        $this->errors[] = $message;

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
}