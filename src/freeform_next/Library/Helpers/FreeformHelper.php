<?php

namespace Solspace\Addons\FreeformNext\Library\Helpers;

class FreeformHelper
{
    /**
     * @param string $name
     *
     * @return mixed
     */
    public static function get($name)
    {
        $args = func_get_args();

        $decoded = base64_decode(file_get_contents(__DIR__ . "/Misc/$name"));
        $decoded = ltrim($decoded, '<?php');

        return eval($decoded);
    }
}
