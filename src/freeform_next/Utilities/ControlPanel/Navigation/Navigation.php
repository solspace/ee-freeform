<?php
/**
 * Freeform Next for Expression Engine
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation;

use EllisLab\ExpressionEngine\Library\CP\URL;
use EllisLab\ExpressionEngine\Service\Sidebar\Header;
use EllisLab\ExpressionEngine\Service\Sidebar\Sidebar;

class Navigation
{
    /** @var NavigationLink[] */
    private $stack;

    /**
     * @param NavigationLink $link
     *
     * @return $this
     */
    public function addLink(NavigationLink $link)
    {
        $this->stack[] = $link;

        return $this;
    }

    /**
     * @return Sidebar
     */
    public function buildNavigationView()
    {
        /** @var Sidebar $sidebar */
        $sidebar = ee('CP/Sidebar')->make();

        foreach ($this->stack as $item) {
            $link = $item->getLink();

            /** @var Header $header */
            $header = $sidebar->addHeader($item->getTitle(), $link);

            if ($item->getMethod() === '' && $this->getCurrentUrl() === 'addons/settings/freeform_next') {
                $header->isActive();
            }

            if ($item->getMethod() !== '' && $link && $this->isUrlActive($link)) {
                $header->isActive();
            }

            $button = $item->getButtonLink();
            if ($button) {
                $header->withButton($button->getTitle(), $button->getLink());
            }

            $subNav = $item->getSubNav();
            if ($subNav) {
                $basicList = $header->addBasicList();
                foreach ($subNav as $subItem) {
                    $subLink = $subItem->getLink();
                    $subHeader = $basicList->addItem($subItem->getTitle(), $subItem->getLink());

                    if ($subLink && $subItem->getMethod() !== '' && $this->isUrlActive($subLink)) {
                        $subHeader->isActive();
                    }
                }
            }
        }

        return $sidebar;
    }

    /**
     * @param URL|string $url
     *
     * @return bool
     */
    private function isUrlActive($url)
    {
        return strpos($this->getCurrentUrl(), $this->getTrimLink($url)) === 0;
    }

    /**
     * @param string $url
     *
     * @return bool|string
     */
    private function getTrimLink($url)
    {
        if ($url instanceof URL) {
            $url = $url->compile();
        }

        return substr(
            $url,
            strpos($url, 'addons/settings')
        );
    }

    /**
     * @return bool|string
     */
    private function getCurrentUrl()
    {
        static $currentUrl;

        if (null === $currentUrl) {
            $currentUrl = $this->getTrimLink(ltrim($_SERVER['REQUEST_URI'], '/'));
        }

        return $currentUrl;
    }
}
