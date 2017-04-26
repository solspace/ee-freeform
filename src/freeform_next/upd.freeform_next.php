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

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Model\StatusModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Utilities\AddonUpdater;
use Solspace\Addons\FreeformNext\Utilities\AddonUpdater\PluginAction;

class Freeform_next_upd extends AddonUpdater
{
    /**
     * @return array
     */
    protected function getInstallableActions()
    {
        return [
            new PluginAction('submitForm', 'Freeform_next', true),
        ];
    }

    /**
     * Perform any actions needed AFTER installing the plugin
     */
    protected function onAfterInstall()
    {
        $fieldRepository = FieldRepository::getInstance();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Address';
        $field->handle = 'address';
        $field->type   = AbstractField::TYPE_TEXTAREA;
        $field->rows   = 2;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Cell Phone';
        $field->handle = 'cell_phone';
        $field->type   = AbstractField::TYPE_TEXT;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'City';
        $field->handle = 'city';
        $field->type   = AbstractField::TYPE_TEXT;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Company Name';
        $field->handle = 'company_name';
        $field->type   = AbstractField::TYPE_TEXT;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Email';
        $field->handle = 'email';
        $field->type   = AbstractField::TYPE_EMAIL;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'First Name';
        $field->handle = 'first_name';
        $field->type   = AbstractField::TYPE_TEXT;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Last Name';
        $field->handle = 'last_name';
        $field->type   = AbstractField::TYPE_TEXT;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Home Phone';
        $field->handle = 'home_phone';
        $field->type   = AbstractField::TYPE_TEXT;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Website';
        $field->handle = 'website';
        $field->type   = AbstractField::TYPE_TEXT;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Zip Code';
        $field->handle = 'zip_code';
        $field->type   = AbstractField::TYPE_TEXT;
        $field->save();

        $field         = $fieldRepository->getOrCreateField();
        $field->label  = 'Message';
        $field->handle = 'message';
        $field->type   = AbstractField::TYPE_TEXTAREA;
        $field->rows   = 5;
        $field->save();

        $states = include __DIR__ . '/states.php';

        $field          = $fieldRepository->getOrCreateField();
        $field->label   = 'State';
        $field->handle  = 'state';
        $field->type    = AbstractField::TYPE_SELECT;
        $field->options = $states;
        $field->save();

        $status            = StatusModel::create();
        $status->name      = 'Open';
        $status->handle    = 'open';
        $status->isDefault = true;
        $status->color     = '#43C413';
        $status->sortOrder = 1;
        $status->save();

        $status            = StatusModel::create();
        $status->name      = 'Closed';
        $status->handle    = 'closed';
        $status->isDefault = false;
        $status->color     = '#939393';
        $status->sortOrder = 2;
        $status->save();

        $status            = StatusModel::create();
        $status->name      = 'Pending';
        $status->handle    = 'pending';
        $status->isDefault = false;
        $status->color     = '#FFFFFF';
        $status->sortOrder = 3;
        $status->save();
    }
}
