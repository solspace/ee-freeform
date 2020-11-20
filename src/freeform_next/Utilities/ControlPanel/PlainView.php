<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel;

use Solspace\Addons\FreeformNext\Utilities\AddonInfo;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\Modal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;

class PlainView extends View
{
    /** @var string */
    private $template;

    /** @var array */
    private $templateVariables;

    /**
     * CpView constructor.
     *
     * @param       $template
     * @param array $templateVariables
     */
    public function __construct($template, array $templateVariables = [])
    {
        $this->template          = $template;
        $this->templateVariables = $templateVariables;
    }

    /**
     * @return string
     */
    public function compile()
    {
        ob_start();
        extract($this->templateVariables, EXTR_SKIP);

        include __DIR__ . "/../../View/{$this->template}.php";

        return ob_get_clean();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return array
     */
    public function getTemplateVariables()
    {
        return $this->templateVariables ?: [];
    }

    /**
     * @param array $templateVariables
     *
     * @return $this
     */
    public function setTemplateVariables($templateVariables)
    {
        $this->templateVariables = $templateVariables;

        return $this;
    }
}
