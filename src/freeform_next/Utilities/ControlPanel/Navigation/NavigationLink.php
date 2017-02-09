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

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation;


use Solspace\Addons\FreeformNext\Utilities\AddonInfo;

class NavigationLink
{
    /** @var string */
    private $title;

    /** @var string */
    private $link;

    /** @var string */
    private $method;

    /** @var string */
    private $subNav;

    /** @var NavigationLink */
    private $buttonLink;

    public function __construct($title, $method = null)
    {
        $this->title = $title;

        if (!is_null($method)) {
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
     * @return array
     */
    public function getLink()
    {
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
*@return $this
     */
    public function setButtonLink(NavigationLink $link = null)
    {
        $this->buttonLink = $link;

        return $this;
    }
}