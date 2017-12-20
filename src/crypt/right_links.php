<?php

$version = \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('version');
$item    = $args[1];


$link     = $title = '';
$showLink = false;

if ($item instanceof \Solspace\Addons\FreeformNext\Controllers\FormController) {
    $count = count(\Solspace\Addons\FreeformNext\Repositories\FormRepository::getInstance()->getAllForms());

    $showLink = $version !== FREEFORM_EXPRESS || $count === 0;
    $link     = 'forms/new';
    $title    = 'New Form';
}

if ($item instanceof \Solspace\Addons\FreeformNext\Controllers\FieldController) {
    $count = count(\Solspace\Addons\FreeformNext\Repositories\FieldRepository::getInstance()->getAllFields());

    $showLink = $version !== FREEFORM_EXPRESS || $count < 15;
    $link     = 'fields/new';
    $title    = 'New Field';
}

if ($item instanceof \Solspace\Addons\FreeformNext\Controllers\StatusController) {
    $showLink = $version !== FREEFORM_EXPRESS;
    $link     = 'settings/statuses/new';
    $title    = 'New Status';
}

if ($showLink) {
    return [
        [
            'title' => lang($title),
            'link'  => \Solspace\Addons\FreeformNext\Library\Helpers\UrlHelper::getLink($link),
        ],
    ];
}

return [];
