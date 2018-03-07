<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

require_once __DIR__ . '/helper_functions.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Library/Helpers/FreeformHelper.php';

if (!session_id()) {
    session_start();
}

if (!defined('FREEFORM_EXPRESS')) {
    define('FREEFORM_EXPRESS', 'express');
    define('FREEFORM_LITE', 'lite');
    define('FREEFORM_PRO', 'pro');
}

return [
    'author'         => 'Solspace',
    'author_url'     => 'https://solspace.com/expressionengine/freeform',
    'docs_url'       => 'https://solspace.com/expressionengine/freeform/docs',
    'name'           => \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('name'),
    'module_name'    => 'Freeform_next',
    'description'    => 'The most intuitive and powerful form builder for ExpressionEngine.',
    'version'        => '1.5.2',
    'namespace'      => 'Solspace\Addons\FreeformNext',
    'settings_exist' => true,
    'models'         => [
        'FormModel'                  => 'Model\FormModel',
        'FieldModel'                 => 'Model\FieldModel',
        'NotificationModel'          => 'Model\NotificationModel',
        'StatusModel'                => 'Model\StatusModel',
        'SubmissionModel'            => 'Model\SubmissionModel',
        'SubmissionPreferencesModel' => 'Model\SubmissionPreferencesModel',
        'SettingsModel'              => 'Model\SettingsModel',
        'IntegrationModel'           => 'Model\IntegrationModel',
        'MailingListModel'           => 'Model\MailingListModel',
        'MailingListFieldModel'      => 'Model\MailingListFieldModel',
        'CrmFieldModel'              => 'Model\CrmFieldModel',
        'ExportProfileModel'         => 'Model\ExportProfileModel',
        'ExportSettingModel'         => 'Model\ExportSettingModel',
    ],
];
