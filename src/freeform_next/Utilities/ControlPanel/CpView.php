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

use Solspace\Addons\FreeformNext\Utilities\AddonInfo;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\Modal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;

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

    /** @var Modal[] */
    private $modals;

    /** @var NavigationLink[] */
    private $breadcrumbs;

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
        $this->modals            = [];
        $this->breadcrumbs       = [];
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

        foreach ($this->modals as $modal) {
            $modal->compile();
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
     * @param array $templateVariables
     *
     * @return $this
     */
    public function addTemplateVariables(array $templateVariables)
    {
        if (null === $this->templateVariables) {
            $this->templateVariables = $templateVariables;

            return $this;
        }

        $this->templateVariables = array_merge($this->templateVariables, $templateVariables);

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

    /**
     * @param Modal $modal
     *
     * @return $this
     */
    public function addModal(Modal $modal)
    {
        $this->modals[] = $modal;

        return $this;
    }

    /**
     * @param NavigationLink $link
     *
     * @return $this
     */
    public function addBreadcrumb(NavigationLink $link)
    {
        $this->breadcrumbs[] = $link;

        return $this;
    }

    /**
     * @return NavigationLink[]
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }
}
