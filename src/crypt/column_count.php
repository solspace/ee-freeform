<?php

$version = \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('version');
$columns = $args[1];

if ($version === FREEFORM_EXPRESS) {
    $newColumns = [];

    foreach ($columns as $column) {
        $data = array_slice($column, 0, count($column) - 2, true);
        $data[1]['content'] = strip_tags($data[1]['content']);

        $newColumns[] = $data;
    }

    return $newColumns;
}

return $columns;
