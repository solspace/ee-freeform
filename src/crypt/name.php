<?php

$version = \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('version');

if ($version === FREEFORM_LITE) {
    return 'Freeform';
}

if ($version === FREEFORM_PRO) {
    return 'Freeform';
}

return 'Freeform';
