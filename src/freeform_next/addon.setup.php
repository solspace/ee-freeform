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

require_once __DIR__ . '/vendor/autoload.php';

if (!session_id()) {
    session_start();
}

return [
    'author'         => 'Solspace',
    'author_url'     => 'https://solspace.com/expressionengine',
    'docs_url'       => 'https://solspace.com/expressionengine/freeform-next/docs',
    'name'           => 'Freeform Next',
    'module_name'    => 'Freeform_next',
    'description'    => 'Advanced form creation and data collecting.',
    'version'        => '1.0.0',
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
    ],
];
