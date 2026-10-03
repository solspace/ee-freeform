<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation;

use EllisLab\ExpressionEngine\Library\CP\URL;
use EllisLab\ExpressionEngine\Service\Sidebar\Header;
use EllisLab\ExpressionEngine\Service\Sidebar\Sidebar;
use Solspace\Addons\FreeformNext\Services\PermissionsService;

class Navigation
{
    /** @var NavigationLink[] */
    private ?array $stack = null;

    /**
     * @return $this
     */
    public function addLink(?NavigationLink $link = null)
    {
        if (null === $link) {
            return $this;
        }

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

        $permissionsService = $this->getPermissionsService();

        foreach ($this->stack as $item) {

            // Special case because resources do not have a method or sub-method
            if ($item->getTitle() == 'Resources') {
                if (!$permissionsService->canUserSeeSectionInNavigation(PermissionsService::PERMISSION__ACCESS_RESOURCES)) continue;
            }

            if ($item->getTitle() == 'Migrations') {
                if (!$permissionsService->canUserSeeSectionInNavigation(PermissionsService::PERMISSION__ACCESS_SETTINGS)) continue;
            }

            // Do not show the section in the menu if user does not have the permission to it
            if (!$permissionsService->canUserSeeSectionInNavigation($item->getMethod())) continue;

            $subNav = $item->getSubNav();

            if ($subNav) {

                $showParentItem = true;

                foreach ($subNav as $subItem) {
                    $subMethod = $subItem->getMethod();
                    $parentMethod = substr($subMethod, 0, strpos($subMethod, "/"));

                    if (!$permissionsService->canUserSeeSectionInNavigation($parentMethod)) {
                        $showParentItem = false;
                    }
                }

                // Do not show the parent item if subsection's permission does not allow that
                if (!$showParentItem) continue;
            }

            $link = $item->getLink();

            /** @var Header $header */
            $header = $sidebar->addHeader($item->getTitle(), $link);

            if ($item->getMethod() === '' && $this->getCurrentUrl() === 'addons/settings/freeform_next') {
                $header->isActive();
            }

            if ($item->getMethod() !== '' && $link && $this->isUrlActive($link)) {
                $header->isActive();
            }

            if (str_starts_with($this->getCurrentUrl(), 'addons/settings/freeform_next/submissions')) {
                if ($item->getMethod() && str_starts_with($item->getMethod(), 'submissions')) {
                    $header->isActive();
                }
            }

            if (str_starts_with($this->getCurrentUrl(), 'addons/settings/freeform_next/spam')) {
                if ($item->getMethod() && str_starts_with($item->getMethod(), 'spam')) {
                    $header->isActive();
                }
            }

            $button = $item->getButtonLink();
            if ($button) {

                $canAddButton = true;

                if ($item->getMethod() === PermissionsService::PERMISSION__MANAGE_FORMS) {
                    $canAddButton = $permissionsService->canManageForms();
                }

                if ($canAddButton) {
                    $header->withButton($button->getTitle(), $button->getLink());
                }
            }

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
    private function isUrlActive($url): bool
    {
        return str_starts_with($this->getCurrentUrl(), $this->getTrimLink($url));
    }

    /**
     * @param string $url
     *
     * @return bool|string
     */
    private function getTrimLink($url): string
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

    /**
     * @return PermissionsService
     */
    private function getPermissionsService()
    {
        static $instance;

        if (null === $instance) {
            $instance = new PermissionsService();
        }

        return $instance;
    }
}
