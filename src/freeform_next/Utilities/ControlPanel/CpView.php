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

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel;

use Solspace\Addons\FreeformNext\Utilities\AddonInfo;

class CpView extends View
{
    /** @var string */
    private $template;

    /** @var array */
    private $templateVariables;

    /** @var string */
    private $heading;

    /** @var array */
    private $cssList;

    /** @var array */
    private $javascriptList;

    /** @var bool */
    private $sidebarDisabled;

    /** @var array */
    private $sections;

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
        $this->cssList           = [];
        $this->javascriptList    = [];
    }

    /**
     * @return string
     */
    public function compile()
    {
        foreach ($this->javascriptList as $path) {
            ee()->cp->load_package_js(preg_replace('/\.js$/is', '', $path));
        }

        foreach ($this->cssList as $path) {
            ee()->cp->load_package_css(preg_replace('/\.css$/is', '', $path));
        }

        return ee('View')
            ->make(AddonInfo::getInstance()->getLowerName() . ':' . $this->template)
            ->render($this->getTemplateVariables());
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

    /**
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * @param string $heading
     *
     * @return $this
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSidebarDisabled()
    {
        return (bool) $this->sidebarDisabled;
    }

    /**
     * @param bool $sidebarDisabled
     *
     * @return $this
     */
    public function setSidebarDisabled($sidebarDisabled)
    {
        $this->sidebarDisabled = (bool) $sidebarDisabled;

        return $this;
    }

    /**
     * @param string $scriptPath
     *
     * @return $this
     */
    public function addJavascript($scriptPath)
    {
        $this->javascriptList[] = $scriptPath;

        return $this;
    }

    /**
     * @param string $cssPath
     *
     * @return $this
     */
    public function addCss($cssPath)
    {
        $this->cssList[] = $cssPath;

        return $this;
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param array $sections
     */
    public function setSections($sections)
    {
        $this->sections = $sections;
    }
}
