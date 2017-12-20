<?php

$version = \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('version');
$columns = $args[1];

if ($version === FREEFORM_EXPRESS) {
   $columns = array_slice($columns, 0, count($columns) - 2, true);
}

return $columns;
