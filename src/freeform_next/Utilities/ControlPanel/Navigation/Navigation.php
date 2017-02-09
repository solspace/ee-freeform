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

use EllisLab\ExpressionEngine\Service\Sidebar\Header;
use EllisLab\ExpressionEngine\Service\Sidebar\Sidebar;

class Navigation
{
    /** @var NavigationLink[] */
    private $stack;

    /**
     * @param NavigationLink $link

     *
*@return $this
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
            /** @var Header $header */
            $header = $sidebar->addHeader($item->getTitle(), $item->getLink());

            $button = $item->getButtonLink();
            if ($button) {
                $header->withButton($button->getTitle(), $button->getLink());
            }
        }

        return $sidebar;
    }
}