<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;

require_once __DIR__ . '/helper_functions.php';
if (version_compare(PHP_VERSION, '8.0.0') < 0) {
	require_once __DIR__ . '/php7/vendor/autoload.php';
}
else
{
	require_once __DIR__ . '/vendor/autoload.php';
}
require_once __DIR__ . '/Library/Helpers/FreeformHelper.php';


if (!defined('FREEFORM_EXPRESS')) {
    define('FREEFORM_EXPRESS', 'express');
    define('FREEFORM_LITE', 'lite');
    define('FREEFORM_PRO', 'pro');
}

$cacheDir = PATH_CACHE . '/freeform_next';
if (FreeformHelper::get('version') !== FREEFORM_EXPRESS && !file_exists($cacheDir . '/ft_check')) {
    $ftExists = ee()->db->where(['name' => 'freeform_next'])->get('exp_fieldtypes')->num_rows();
    if (!$ftExists) {
        ee()->db->insert('exp_fieldtypes', [
            'name'                => 'freeform_next',
            'version'             => '3.0.0-b.1',
            'settings'            => 'YTowOnt9',
            'has_global_settings' => 'n',
        ]);
    }


    if (!file_exists($cacheDir)) {
        if (@mkdir($cacheDir) || is_dir($cacheDir)) {
            @touch($cacheDir . '/ft_check');
        }
    } else {
        @touch($cacheDir . '/ft_check');
    }
}

return [
    'author'         => 'Solspace',
    'author_url'     => 'https://docs.solspace.com/expressionengine/freeform/v3/',
    'docs_url'       => 'https://docs.solspace.com/expressionengine/freeform/v3/',
    'name'           => 'Freeform',
    'module_name'    => 'Freeform_next',
    'description'    => 'The most reliable, intuitive and powerful form builder for ExpressionEngine.',
    'version'        => '3.0.0-b.1',
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
        'PermissionsModel'           => 'Model\PermissionsModel',
        'IntegrationModel'           => 'Model\IntegrationModel',
        'MailingListModel'           => 'Model\MailingListModel',
        'MailingListFieldModel'      => 'Model\MailingListFieldModel',
        'CrmFieldModel'              => 'Model\CrmFieldModel',
        'ExportProfileModel'         => 'Model\ExportProfileModel',
        'ExportSettingModel'         => 'Model\ExportSettingModel',
    ],
];
