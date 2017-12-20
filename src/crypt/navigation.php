<?php

$version = \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('version');

/** @var \Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink $item */
$item = $args[1];

$link = '';
$showLink = false;

if ($item->getMethod() === 'fields') {
    $count = count(\Solspace\Addons\FreeformNext\Repositories\FieldRepository::getInstance()->getAllFields());

    $link = 'fields/new';
    $showLink = $version !== FREEFORM_EXPRESS || ($count < 15);
}

if ($item->getMethod() === 'forms') {
    $count = count(\Solspace\Addons\FreeformNext\Repositories\FormRepository::getInstance()->getAllForms());

    $link = 'forms/new';
    $showLink = $version !== FREEFORM_EXPRESS || ($count < 1);
}

if ($showLink) {
    $item->setButtonLink(new \Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink('New', $link));
}
