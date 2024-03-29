<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components;

interface FieldInterface
{
    const TYPE_TEXT               = 'text';
    const TYPE_TEXTAREA           = 'textarea';
    const TYPE_HIDDEN             = 'hidden';
    const TYPE_SELECT             = 'select';
    const TYPE_MULTIPLE_SELECT    = 'multiple_select';
    const TYPE_CHECKBOX           = 'checkbox';
    const TYPE_CHECKBOX_GROUP     = 'checkbox_group';
    const TYPE_RADIO_GROUP        = 'radio_group';
    const TYPE_HTML               = 'html';
    const TYPE_SUBMIT             = 'submit';
    const TYPE_DYNAMIC_RECIPIENTS = 'dynamic_recipients';
    const TYPE_EMAIL              = 'email';
    const TYPE_MAILING_LIST       = 'mailing_list';
    const TYPE_FILE               = 'file';
    const TYPE_PASSWORD           = 'password';

    const TYPE_DATETIME     = 'datetime';
    const TYPE_NUMBER       = 'number';
    const TYPE_PHONE        = 'phone';
    const TYPE_WEBSITE      = 'website';
    const TYPE_RATING       = 'rating';
    const TYPE_REGEX        = 'regex';
    const TYPE_CONFIRMATION = 'confirmation';
    const TYPE_RECAPTCHA    = 'recaptcha';
    const TYPE_TABLE        = 'table';

    /**
     * Returns the INPUT type
     *
     * @return string
     */
    public function getType();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * Gets whatever value is set and returns its string representation
     *
     * @return string
     */
    public function getValueAsString();

    /**
     * @return string
     */
    public function getHandle();

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value);

    /**
     * Returns an array of error messages
     *
     * @return array
     */
    public function getErrors();

    /**
     * @return bool
     */
    public function hasErrors();

    /**
     * Renders the <label> and <input> tags combined
     *
     * @return string
     */
    public function render();

    /**
     * Renders the <label> tag
     *
     * @return string
     */
    public function renderLabel();

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function renderInput();

    /**
     * Outputs the HTML of errors
     *
     * @return string
     */
    public function renderErrors();

    /**
     * Validates the Field value
     *
     * @return bool
     */
    public function isValid();
}
