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
     * @param string|null $previousVersion
     *
     * @return bool
     */
    public function update($previousVersion = null)
    {
        if (version_compare($previousVersion, '1.0.3', '<=')) {
            ee()->db
                ->query('
                CREATE TABLE IF NOT EXISTS `exp_freeform_next_session_data`
                (
                  `sessionId`   VARCHAR(255) NOT NULL,
                  `key`         VARCHAR(255) NOT NULL,
                  `data`        TEXT         NULL,
                  `dateCreated` DATETIME     NOT NULL,
                  PRIMARY KEY (`sessionId`, `key`)
                )
                  ENGINE = InnoDB
                  DEFAULT CHARSET = `utf8`
                  COLLATE = `utf8_unicode_ci`
              ');

            ee()->db
                ->query('
                    ALTER TABLE exp_freeform_next_settings
                    ADD COLUMN `sessionStorage` ENUM(\'session\', \'db\') DEFAULT \'session\' AFTER `license`
                ');
        }

        if (version_compare($previousVersion, '1.1.0', '<')) {
            ee()->db
                ->query('
                    ALTER TABLE exp_freeform_next_fields
                    ADD COLUMN `additionalProperties` TEXT DEFAULT NULL AFTER `maxFileSizeKB`
                ');

            ee()->db
                ->query('
                CREATE TABLE IF NOT EXISTS `exp_freeform_next_export_profiles` (
                  `id`          INT(11)      NOT NULL  AUTO_INCREMENT,
                  `siteId`      INT(11)      NOT NULL  DEFAULT \'1\',
                  `name`        VARCHAR(255) NULL,
                  `formId`      INT(11) NOT NULL,
                  `limit`       INT(11) NULL,
                  `dateRange`   VARCHAR(255) NULL,
                  `fields`      TEXT NULL,
                  `filters`     TEXT NULL,
                  `statuses`    TEXT NULL,
                  `dateCreated` DATETIME DEFAULT NULL,
                  `dateUpdated` DATETIME DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `exp_freeform_next_export_formId_fk` (`formId`)
                )
                  ENGINE = InnoDB
                  DEFAULT CHARSET = `utf8`
                  COLLATE = `utf8_unicode_ci`
                ');

            ee()->db
                ->query('
                CREATE TABLE IF NOT EXISTS `exp_freeform_next_export_settings` (
                  `id`          INT(11)      NOT NULL  AUTO_INCREMENT,
                  `siteId`      INT(11)      NOT NULL  DEFAULT \'1\',
                  `userId`      INT(11)      NOT NULL,
                  `settings`    TEXT         NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `exp_freeform_next_export_userId_fk` (`userId`)
                )
                  ENGINE = InnoDB
                  DEFAULT CHARSET = `utf8`
                  COLLATE = `utf8_unicode_ci`;
                ');
        }

        return true;
    }

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
        $status->color     = '#8cc258';
        $status->sortOrder = 1;
        $status->save();

        $status            = StatusModel::create();
        $status->name      = 'Closed';
        $status->handle    = 'closed';
        $status->isDefault = false;
        $status->color     = '#df4537';
        $status->sortOrder = 2;
        $status->save();

        $status            = StatusModel::create();
        $status->name      = 'Pending';
        $status->handle    = 'pending';
        $status->isDefault = false;
        $status->color     = '#ffe35b';
        $status->sortOrder = 3;
        $status->save();
    }
}
