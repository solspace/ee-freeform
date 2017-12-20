<?php
/**
 * Freeform for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
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
                ->query(
                    '
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
              '
                );

            ee()->db
                ->query(
                    '
                    ALTER TABLE exp_freeform_next_settings
                    ADD COLUMN `sessionStorage` ENUM(\'session\', \'db\') DEFAULT \'session\' AFTER `license`
                '
                );
        }

        if (version_compare($previousVersion, '1.1.0', '<')) {
            ee()->db
                ->query(
                    '
                    ALTER TABLE exp_freeform_next_fields
                    ADD COLUMN `additionalProperties` TEXT DEFAULT NULL AFTER `maxFileSizeKB`
                '
                );

            ee()->db
                ->query(
                    '
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
                '
                );

            ee()->db
                ->query(
                    '
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
                '
                );

            ee()->db
                ->query(
                    '
                    ALTER TABLE exp_freeform_next_settings
                    ADD COLUMN `defaultTemplates` TINYINT(1) DEFAULT 1 AFTER `sessionStorage`
                '
                );
        }

        if (version_compare($previousVersion, '1.1.3', '<')) {
            ee()->db
                ->query(
                    '
                    ALTER TABLE exp_freeform_next_settings
                    ADD COLUMN `formSubmitDisable` TINYINT(1) DEFAULT 1 AFTER `defaultTemplates`
                '
                );
        }

        if (version_compare($previousVersion, '1.3.1', '<=')) {
            ee()->db
                ->query(
                    '
                    INSERT INTO exp_actions(`class`, `method`, `csrf_exempt`) VALUES ("Freeform_next", "validateForm", 1)
                '
                );
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
            new PluginAction('validateForm', 'Freeform_next', true),
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

        ee()->db->insert(
            'freeform_next_statuses',
            [
                'name'      => 'Open',
                'handle'    => 'open',
                'isDefault' => true,
                'color'     => '#8cc258',
                'sortOrder' => 1,
            ]
        );

        ee()->db->insert(
            'freeform_next_statuses',
            [
                'name'      => 'Closed',
                'handle'    => 'closed',
                'isDefault' => false,
                'color'     => '#df4537',
                'sortOrder' => 2,
            ]
        );

        ee()->db->insert(
            'freeform_next_statuses',
            [
                'name'      => 'Pending',
                'handle'    => 'pending',
                'isDefault' => false,
                'color'     => '#ffe35b',
                'sortOrder' => 3,
            ]
        );
    }
}
