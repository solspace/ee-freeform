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
        $currentUrl = ltrim($_SERVER['REQUEST_URI'], '/');

        /** @var Sidebar $sidebar */
        $sidebar = ee('CP/Sidebar')->make();

        foreach ($this->stack as $item) {
            $link = $item->getLink();

            /** @var Header $header */
            $header = $sidebar->addHeader($item->getTitle(), $link);
            if ($item->getMethod() !== '' && $link && strpos($currentUrl, $link->compile()) === 0) {
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
                    $basicList->addItem($subItem->getTitle(), $subItem->getLink());
                }
            }
        }

        return $sidebar;
    }
}
