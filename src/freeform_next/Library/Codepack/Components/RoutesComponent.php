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

namespace Solspace\Addons\FreeformNext\Library\Codepack\Components;

use Solspace\Addons\FreeformNext\Library\Codepack\Codepack;

class RoutesComponent extends AbstractJsonComponent
{
    /**
     * Calls the installation of this component
     *
     * @param string $prefix
     */
    public function install($prefix = null)
    {
        $data       = $this->getData();
        $demoFolder = $prefix . "/";

        foreach ($data as $route) {
            if (isset($route->urlParts) && is_array($route->urlParts) && isset($route->template)) {
                $urlParts = $route->urlParts;

                array_walk_recursive($urlParts, function(&$value) {
                    $value = stripslashes($value);
                });

                $urlParts[0] = $demoFolder . $urlParts[0];

                $pattern  = "/(\/?)(.*)/";
                $template = preg_replace($pattern, "$1$demoFolder$2", $route->template, 1);

                // $routeService->saveRoute($urlParts, $template);
            }
        }
    }

    /**
     * This is the method that sets all vital properties
     * ::$fileName
     */
    protected function setProperties()
    {
        $this->fileName = 'routes.json';
    }
}
