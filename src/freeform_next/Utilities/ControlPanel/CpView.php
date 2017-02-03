<?php

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel;

use Solspace\Addons\FreeformNext\Utilities\AddonInfo;

class CpView
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
    public function renderBody()
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
}