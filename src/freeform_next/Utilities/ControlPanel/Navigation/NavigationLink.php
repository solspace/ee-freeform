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

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation;


use EllisLab\ExpressionEngine\Library\CP\URL;
use Solspace\Addons\FreeformNext\Utilities\AddonInfo;

class NavigationLink
{
    /** @var string */
    private $title;

    /** @var string */
    private $link;

    /** @var string */
    private $method;

    /** @var NavigationLink[] */
    private $subNav;

    /** @var NavigationLink */
    private $buttonLink;

    /**
     * NavigationLink constructor.
     *
     * @param string $title
     * @param string $method
     */
    public function __construct($title, $method = null)
    {
        $this->title  = $title;
        $this->subNav = [];

        if (null !== $method) {
            $this->method = $method;
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return lang($this->title);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return URL|string|null
     */
    public function getLink()
    {
        if (null === $this->method) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $this->method)) {
            return $this->method;
        }

        $addonInfo = AddonInfo::getInstance();

        $link = '';
        if ($this->method) {
            $link = ee('CP/URL', 'addons/settings/' . $addonInfo->getLowerName() . '/' . $this->method);
        }

        if (empty($link)) {
            $link = ee('CP/URL', 'addons/settings/' . $addonInfo->getLowerName());
        }

        return $link;
    }

    /**
     * @return NavigationLink
     */
    public function getButtonLink()
    {
        return $this->buttonLink;
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param NavigationLink|null $link
     *
     * @return $this
     */
    public function setButtonLink(NavigationLink $link = null)
    {
        $this->buttonLink = $link;

        return $this;
    }

    /**
     * @param NavigationLink $link
     *
     * @return $this
     */
    public function addSubNavItem(NavigationLink $link)
    {
        $this->subNav[] = $link;

        return $this;
    }

    /**
     * @return array|NavigationLink[]
     */
    public function getSubNav()
    {
        return $this->subNav;
    }
}
