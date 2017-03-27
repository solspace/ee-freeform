<?php

namespace Solspace\Addons\FreeformNext\Library\Helpers;

class UrlHelper
{
    /**
     * @param $target
     *
     * @return string
     */
    public static function getLink($target)
    {
        return ee('CP/URL', 'addons/settings/freeform_next/' . $target);
    }
}
