<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\UrlHelper;
use Solspace\Addons\FreeformNext\Model\PermissionsModel;
use Solspace\Addons\FreeformNext\Model\SettingsModel;
use Solspace\Addons\FreeformNext\Model\StatusModel;
use Solspace\Addons\FreeformNext\Repositories\PermissionsRepository;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Utilities\AddonInfo;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;
use Stringy\Stringy;
use Symfony\Component\PropertyAccess\PropertyAccess;
use ExpressionEngine\Model\Member\Role;

class SettingsController extends Controller
{
    const TYPE_STATUSES             = 'statuses';
    const TYPE_LICENSE              = 'license';
    const TYPE_GENERAL              = 'general';
    const TYPE_SPAM_PROTECTION      = 'spam_protection';
    const TYPE_PERMISSIONS          = 'permissions';
    const TYPE_FORMATTING_TEMPLATES = 'formatting_templates';
    const TYPE_EMAIL_TEMPLATES      = 'email_templates';
    const TYPE_DEMO_TEMPLATES       = 'demo_templates';
    const TYPE_RECAPTCHA            = 'recaptcha';

    /** @var array */
    private static $allowedTypes = [
        self::TYPE_STATUSES,
        self::TYPE_LICENSE,
        self::TYPE_GENERAL,
        self::TYPE_SPAM_PROTECTION,
        self::TYPE_PERMISSIONS,
        self::TYPE_FORMATTING_TEMPLATES,
        self::TYPE_EMAIL_TEMPLATES,
        self::TYPE_DEMO_TEMPLATES,
        self::TYPE_RECAPTCHA,
    ];

    /**
     * @param string $type
     * @param int    $id
     *
     * @return View
     * @throws FreeformException
     */
    public function index($type, $id)
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings(ee()->session->userdata('group_id'));

        if (!$canAccessSettings) {
            return new RedirectView($this->getLink('denied'));
        }

        if (!in_array($type, self::$allowedTypes, true)) {
            throw new FreeformException('Page does not exist');
        }

        if ($type !== 'statuses' && $this->handlePost($type)) {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('Success'))
                ->defer();

            return new RedirectView($this->getLink('settings/' . $type));
        }

        switch ($type) {
            case self::TYPE_STATUSES:
                return $this->statusesAction($id);

            case self::TYPE_LICENSE:
                return $this->licenseAction();

            case self::TYPE_FORMATTING_TEMPLATES:
                return $this->formattingTemplatesAction();

            case self::TYPE_EMAIL_TEMPLATES:
                return $this->emailTemplatesAction();

            case self::TYPE_DEMO_TEMPLATES:
                return $this->demoTemplatesAction();

            case self::TYPE_PERMISSIONS:
                return $this->permissionsAction();

            case self::TYPE_RECAPTCHA:
                return $this->recaptchaAction();

			case self::TYPE_SPAM_PROTECTION:
				return $this->spamProtectionAction();

            case self::TYPE_GENERAL:
            default:
                return $this->generalAction();
        }
    }

    /**
     * @param null|string|int $id
     *
     * @return View
     * @throws FreeformException
     */
    public function statusesAction($id = null)
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings(ee()->session->userdata('group_id'));

        if (!$canAccessSettings) {
            return new RedirectView($this->getLink('denied'));
        }

        if ($id && strtolower($id) === 'delete') {
            return $this->getStatusController()->batchDelete();
        }

        if (null !== $id) {
            $validation = null;
            if (isset($_POST['name'])) {
                $validation = ee('Validation')->make(StatusModel::createValidationRules())->validate($_POST);
                if ($validation->isValid()) {
                    $this->getStatusController()->save($id);

                    return new RedirectView(UrlHelper::getLink('settings/statuses/'));
                }
            }

            return $this->getStatusController()->edit($id, $validation);
        }

        return $this->getStatusController()->index();
    }

    /**
     * @return CpView
     */
    private function licenseAction()
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings(ee()->session->userdata('group_id'));

        if (!$canAccessSettings) {
            return new RedirectView($this->getLink('denied'));
        }

        $settings = $this->getSettings();

        $view = new CpView('settings/common', []);
        $view
            ->setHeading(lang('License'))
            ->addBreadcrumb(new NavigationLink('Settings', 'settings/general'))
            ->setTemplateVariables(
                [
                    'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
                    'cp_page_title'         => $view->getHeading(),
                    'save_btn_text'         => 'btn_save_settings',
                    'save_btn_text_working' => 'btn_saving',
                    'sections'              => [
                        [
                            [
                                'title'  => 'License',
                                'desc'   => 'Enter your Freeform license key here.',
                                'fields' => [
                                    'license' => [
                                        'type'  => 'text',
                                        'value' => $settings->license,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );

        return $view;
    }

    /**
     * @return View
     */
    public function permissionDenied()
    {
        $pageTitle = lang('Permission Denied');

        $view = new CpView(
            'settings/permission-denied',
            [
                'cp_page_title'    => $pageTitle,
                'form_right_links' => [],
            ]
        );
        $view->setHeading($pageTitle);

        return $view;
    }

    /**
     * @return CpView
     */
    private function generalAction()
    {
        $settings = $this->getSettings();

        $view = new CpView('settings/common', []);
        $view
            ->setHeading(lang('General'))
            ->addBreadcrumb(new NavigationLink('Settings', 'settings/general'))
            ->setTemplateVariables(
                [
                    'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
                    'cp_page_title'         => $view->getHeading(),
                    'save_btn_text'         => 'btn_save_settings',
                    'save_btn_text_working' => 'btn_saving',
                    'sections'              => [
                        [
                            // [
                            //     'title'  => 'Show Composer Tutorial',
                            //     'desc'   => 'Enable this to show the interactive tutorial again in Composer. This setting disables again when the tutorial is completed or skipped.',
                            //     'fields' => [
                            //         'showTutorial' => [
                            //             'type'  => 'yes_no',
                            //             'value' => $settings->isShowTutorial(),
                            //         ],
                            //     ],
                            // ],
                            [
                                'title'  => 'Session Storage Mechanism',
                                'desc'   => 'Choose the mechanism with which session data is stored on front end submissions.',
                                'fields' => [
                                    'sessionStorage' => [
                                        'type'    => 'radio',
                                        'value'   => $settings->sessionStorage,
                                        'choices' => [
                                            SettingsModel::SESSION_STORAGE_SESSION  => 'PHP Sessions',
                                            SettingsModel::SESSION_STORAGE_DATABASE => 'Database',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Display Order of Fields in Composer',
                                'desc'   => 'The display order for the list of available fields in Composer.',
                                'fields' => [
                                    'fieldDisplayOrder' => [
                                        'type'    => 'radio',
                                        'value'   => $settings->getFieldDisplayOrder(),
                                        'choices' => [
                                            SettingsModel::FIELD_DISPLAY_ORDER_TYPE => 'Field type, Field name (alphabetical)',
                                            SettingsModel::FIELD_DISPLAY_ORDER_NAME => 'Field name (alphabetical)',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Include Default Freeform Formatting Templates',
                                'desc'   => 'Disable this to hide the default Freeform formatting templates in the Formatting Template options list inside Composer.',
                                'fields' => [
                                    'defaultTemplates' => [
                                        'type'  => 'yes_no',
                                        'value' => $settings->isDefaultTemplates() ? 'y' : 'n',
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Remove Newlines from Textareas for Exporting',
                                'desc'   => 'Enable this to have newlines removed from Textarea fields in submissions when exporting.',
                                'fields' => [
                                    'removeNewlines' => [
                                        'type'  => 'yes_no',
                                        'value' => $settings->removeNewlines ? 'y' : 'n',
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Disable Submit Button on Form Submit',
                                'desc'   => 'Enable this to automatically disable the form\'s submit button when the user submits the form. This will prevent the form from double-submitting.',
                                'fields' => [
                                    'formSubmitDisable' => [
                                        'type'  => 'yes_no',
                                        'value' => $settings->isFormSubmitDisable() ? 'y' : 'n',
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Automatically Scroll to Form on Errors and Multipage forms?',
                                'desc'   => 'Enable this to have Freeform use JS to automatically scroll the page down to the form upon submit when there are errors or the form is continuing to the next page in multipage forms.',
                                'fields' => [
                                    'autoScrollToErrors' => [
                                        'type'  => 'yes_no',
                                        'value' => $settings->isAutoScrollToErrors() ? 'y' : 'n',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );

        return $view;
    }

	/**
	 * @return CpView
	 */
	private function spamProtectionAction()
	{
		$settings = $this->getSettings();

		$view = new CpView('settings/common', []);
		$view
			->setHeading(lang('Spam Protection'))
			->addBreadcrumb(new NavigationLink('Settings', 'settings/spam_protection'))
			->setTemplateVariables(
				[
					'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
					'cp_page_title'         => $view->getHeading(),
					'save_btn_text'         => 'btn_save_settings',
					'save_btn_text_working' => 'btn_saving',
					'sections'              => [
						[
							[
								'title'  => 'Freeform Honeypot',
								'desc'   => 'Enable this to use Freeform\'s built in Honeypot spam protection.',
								'fields' => [
									'spamProtectionEnabled' => [
										'type'  => 'yes_no',
										'value' => $settings->isSpamProtectionEnabled(),
									],
								],
							],
							[
								'title'  => 'Javascript Enhancement',
								'desc'   => 'Enable this to use Freeform\'s built-in Javascript enhancement for the Honeypot feature. This will require users to have JS enabled for their browser and help fight spambots more aggressively.',
								'fields' => [
									'freeformHoneypotEnhancement' => [
										'type'  => 'yes_no',
										'value' => $settings->isFreeformHoneypotEnhanced(),
									],
								],
							],
							[
								'title'  => 'Spam protection simulates a successful submission?',
								'desc'   => 'Enable this to change the spam protection behavior to simulate a successful submission instead of just reloading the form.',
								'fields' => [
									'spamBlockLikeSuccessfulPost' => [
										'type'  => 'yes_no',
										'value' => $settings->isSpamBlockLikeSuccessfulPost(),
									],
								],
							],
						],
					],
				]
			);

		return $view;
	}
    /**
     * @return CpView
     */
    private function permissionsAction()
    {
        $version = \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('version');

        $permissionsModel = $this->getPermissionsModel();

        /** @var Role[] $memberRoles */
        $memberRoles = ee('Model')->get('Role')
            ->with('AssignedChannels')
            ->fields('role_id')
            ->fields('name')
            ->all();

        $memberRoleChoices = [];
        foreach ($memberRoles as $role) {
            if ($role->role_id === 1) {
                continue;
            }

            $memberRoleChoices[$role->role_id] = $role->name;
        }

        $view = new CpView('settings/common', []);

        $sections = [
            [
                'title'  => 'Default Permissions for New Member Groups',
                'fields' => [
                    'defaultPermissions' => [
                        'type'    => 'radio',
                        'value'   => $permissionsModel->defaultPermissions,
                        'choices' => [
                            'allow_all' => 'Allow All Access',
                            'deny_all' => 'Deny All Access',
                        ],
                    ],
                ],
            ],
            [
                'title'  => 'Manage Forms',
                'desc'   => 'Choose which member groups can manage Forms.',
                'fields' => [
                    'formsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->formsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
            [
                'title'  => 'Access Submissions',
                'desc'   => 'Choose which member groups have access to Submissions.',
                'fields' => [
                    'submissionsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->submissionsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
            [
                'title'  => 'Manage Submissions',
                'desc'   => 'Choose which member groups can manage Submissions.',
                'fields' => [
                    'manageSubmissionsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->manageSubmissionsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
            [
                'title'  => 'Access Fields',
                'desc'   => 'Choose which member groups have access to the Field Manager.',
                'fields' => [
                    'fieldsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->fieldsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
            [
                'title'  => 'Access Notifications',
                'desc'   => 'Choose which member groups have access to Notifications.',
                'fields' => [
                    'notificationsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->notificationsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
        ];

        if ($version === 'pro') {
            $sections[] = [
                'title'  => 'Access Export',
                'desc'   => 'Choose which member groups have access to Export.',
                'fields' => [
                    'exportPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->exportPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ];
        }

        $additionalSections = [
            [
                'title'  => 'Access Settings',
                'desc'   => 'Choose which member groups have access to Settings.',
                'fields' => [
                    'settingsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->settingsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
            [
                'title'  => 'Access Integrations',
                'desc'   => 'Choose which member groups have access to Integrations.',
                'fields' => [
                    'integrationsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->integrationsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
            [
                'title'  => 'Access Logs',
                'desc'   => 'Choose which member groups have access to Error logs.',
                'fields' => [
                    'logsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->logsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
        ];

        $sections = array_merge($sections, $additionalSections);

        $fields = [
            'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
            'cp_page_title'         => lang('Permissions'),
            'save_btn_text'         => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
            'sections'              => [$sections],
        ];

        $view
            ->setHeading(lang('Permissions'))
            ->addBreadcrumb(new NavigationLink('Settings', 'settings/general'))
            ->setTemplateVariables($fields);

        return $view;
    }

    /**
     * @return CpView
     */
    private function formattingTemplatesAction()
    {
        $settings = $this->getSettings();

        $variables = [
            'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
            'cp_page_title'         => lang('Formatting Templates'),
            'save_btn_text'         => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
            'sections'              => [
                [
                    [
                        'title'  => 'Directory Path',
                        'desc'   => 'Provide a relative path from the EE \'/system/user/templates/\' directory, or full path to the folder where your custom formatting templates directory is. This allows you to use HTML templates for your form formatting, and helps Composer locate these files to assign one of them to a form.',
                        'fields' => [
                            'formattingTemplatePath' => [
                                'type'        => 'text',
                                'value'       => $settings->getFormattingTemplatePath(),
                                'placeholder' => PATH_TMPL,
                            ],
                        ],
                    ],
                ],
            ],
        ];


        if ($settings->getFormattingTemplatePath()) {
            $path  = $settings->getFormattingTemplatePath();
            $files = $settings->listTemplatesInFormTemplateDirectory();
            $url   = $this->getLink('templates');

            ob_start();
            include(PATH_THIRD . 'freeform_next/Templates/notifications/listing.php');
            $content = ob_get_clean();

            $variables['sections'][0][1] = [
                'title'  => '',
                'wide'   => true,
                'fields' => [
                    'listing' => [
                        'type'    => 'html',
                        'content' => $content,
                    ],
                ],
            ];
        }

        $view = new CpView('settings/common', $variables);
        $view
            ->setHeading(lang('Formatting Templates'))
            ->addBreadcrumb(new NavigationLink('Settings', 'settings/general'))
            ->addJavascript('settings');


        return $view;
    }

    /**
     * @return CpView
     */
    private function emailTemplatesAction()
    {
        $settings = $this->getSettings();

        $variables = [
            'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
            'cp_page_title'         => lang('Email Templates'),
            'save_btn_text'         => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
            'sections'              => [
                [
                    [
                        'title'  => 'Directory Path',
                        'desc'   => 'Provide a relative path from the EE \'/system/user/templates/\' directory, or full path to the folder where your email templates directory is. This allows you to use HTML template files for your email formatting, and helps Composer locate these files when setting up notifications.',
                        'fields' => [
                            'notificationTemplatePath' => [
                                'type'        => 'text',
                                'value'       => $settings->getNotificationTemplatePath(),
                                'placeholder' => PATH_TMPL,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if ($settings->getNotificationTemplatePath()) {
            $variables['sections'][0][1] = [
                'title'  => 'Default Email Notification Creation Method',
                'desc'   => 'Which storage method to use when creating new email notifications with \'Add New Notification\' option in Composer.',
                'fields' => [
                    'notificationCreationMethod' => [
                        'type'    => 'select',
                        'value'   => $settings->getNotificationCreationMethod(),
                        'choices' => [
                            SettingsModel::NOTIFICATION_CREATION_METHOD_DATABASE => 'Database Entry',
                            SettingsModel::NOTIFICATION_CREATION_METHOD_TEMPLATE => 'Template File',
                        ],
                    ],
                ],
            ];

            $path  = $settings->getNotificationTemplatePath();
            $files = $settings->listTemplatesInEmailTemplateDirectory();
            $url   = $this->getLink('api/notifications/create');

            ob_start();
            include(PATH_THIRD . 'freeform_next/Templates/notifications/listing.php');
            $content = ob_get_clean();

            $variables['sections'][0][2] = [
                'title'  => '',
                'wide'   => true,
                'fields' => [
                    'listing' => [
                        'type'    => 'html',
                        'content' => $content,
                    ],
                ],
            ];
        }

        $view = new CpView('settings/common', $variables);
        $view
            ->setHeading(lang('Email Templates'))
            ->addBreadcrumb(new NavigationLink('Settings', 'settings/general'))
            ->addJavascript('settings');

        return $view;
    }

    /**
     * @return View
     */
    private function demoTemplatesAction()
    {
        $controller = new DemoTemplatesController();

        return $controller->index();
    }

    /**
     * @return View
     */
    private function recaptchaAction()
    {
        $settings = $this->getSettings();

        $variables = [
            'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
            'cp_page_title'         => lang('reCAPTCHA'),
            'save_btn_text'         => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
            'sections'              => [
                [
                    [
                        'title'  => 'reCAPTCHA Enabled?',
                        'fields' => [
                            'recaptchaEnabled' => [
                                'type'        => 'yes_no',
                                'value'       => $settings->isRecaptchaEnabled() ? 'y' : 'n',
                            ],
                        ],
                    ],
                    [
                        'title'  => 'reCAPTCHA Type',
                        'desc' => 'Choose the reCAPTCHA type to use. The options below are compatible with the Enterprise API and the Classic legacy keys.',
                        'fields' => [
                            'recaptchaType' => [
                                'type' => 'select',
                                'value' => $settings->getRecaptchaType(),
                                'choices' => [
                                    'v2-checkbox' => 'Challenge - Checkbox (v2)',
                                    'v3' => 'Score Based (v3)',
                                ],
                                'group_toggle' => [
                                    'v3' => 'recaptchaScoreThresholdOptions',
                                ],
                            ],
                        ],
                    ],
                    [
                        'title'  => 'reCAPTCHA Site Key',
                        'fields' => [
                            'recaptchaKey' => [
                                'type'        => 'text',
                                'value'       => $settings->getRecaptchaKey(),
                            ],
                        ],
                    ],
                    [
                        'title'  => 'reCAPTCHA Secret Key',
                        'fields' => [
                            'recaptchaSecret' => [
                                'type'        => 'text',
                                'value'       => $settings->getRecaptchaSecret(),
                            ],
                        ],
                    ],
                    [
                        'title' => 'reCAPTCHA Score Threshold',
                        'desc' => 'The minimum score required for the Captcha to pass validation. The score is a number between 0 and 1. A score of 0.5 is generally recommended.',
                        'group' => 'recaptchaScoreThresholdOptions',
                        'fields' => [
                            'recaptchaScoreThreshold' => [
                                'type' => 'select',
                                'value' => $settings->getRecaptchaScoreThreshold() ?: '0.5',
                                'choices' => [
                                    '0.0' => '0.0',
                                    '0.1' => '0.1',
                                    '0.2' => '0.2',
                                    '0.3' => '0.3',
                                    '0.4' => '0.4',
                                    '0.5' => '0.5',
                                    '0.6' => '0.6',
                                    '0.7' => '0.7',
                                    '0.8' => '0.8',
                                    '0.9' => '0.9',
                                    '1.0' => '1.0',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $view = new CpView('settings/common', $variables);
        $view
            ->setHeading(lang('reCAPTCHA'))
            ->addBreadcrumb(new NavigationLink('Settings', 'settings/general'))
            ->addJavascript('settings');

        return $view;
    }

    /**
     * Handles a POST request and returns one of the following
     * TRUE  - if it was handled
     * FALSE - if there were errors
     * NULL  - if nothing was posted
     *
     * @return bool
     */
    private function handlePost($type)
    {
        if ($type == self::TYPE_PERMISSIONS) {
            $settings = $this->getPermissionsModel();
        } else {
            $settings = $this->getSettings();
        }

        if (!empty($_POST) && !isset($_POST['prefix'])) {
            $accessor = PropertyAccess::createPropertyAccessor();

            foreach ($_POST as $key => $value) {
                if ($accessor->isWritable($settings, $key)) {
                    $value = ee()->input->post($key);
                    if ($value === 'y' || $value === 'n') {
                        $value = $value === 'y';
                    }

                    $accessor->setValue($settings, $key, $value);
                }
            }

            $settings->save();

            return true;
        }

        return null;
    }

    /**
     * @return SettingsModel
     */
    private function getSettings()
    {
        return SettingsRepository::getInstance()->getOrCreate();
    }

    /**
     * @return PermissionsModel
     */
    private function getPermissionsModel()
    {
        return PermissionsRepository::getInstance()->getOrCreate();
    }

    /**
     * @param string $method
     *
     * @return string
     */
    private function getActionUrl($method)
    {
        $target = (string) Stringy::create($method)->underscored();
        $target = str_replace('_action', '', $target);

        return 'addons/settings/' . AddonInfo::getInstance()->getLowerName() . '/settings/' . $target;
    }

    /**
     * @return StatusController
     */
    private function getStatusController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new StatusController();
        }

        return $instance;
    }
}
