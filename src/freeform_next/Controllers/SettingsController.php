<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
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
    public const TYPE_STATUSES             = 'statuses';
    public const TYPE_LICENSE              = 'license';
    public const TYPE_GENERAL              = 'general';
    public const TYPE_SPAM_PROTECTION      = 'spam_protection';
    public const TYPE_PERMISSIONS          = 'permissions';
    public const TYPE_FORMATTING_TEMPLATES = 'formatting_templates';
    public const TYPE_EMAIL_TEMPLATES      = 'email_templates';
    public const TYPE_DEMO_TEMPLATES       = 'demo_templates';
    public const TYPE_RECAPTCHA            = 'recaptcha';

    private static array $allowedTypes = [
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
     * @param int    $id
     *
     * @return View
     * @throws FreeformException
     */
    public function index(string $type, $id)
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings();

        if (!$canAccessSettings) {
            return new RedirectView($this->getLink('denied'));
        }

        if (!in_array($type, self::$allowedTypes, true)) {
            throw new FreeformException('Page does not exist');
        }

        if ($type === self::TYPE_RECAPTCHA) {
            return new RedirectView($this->getLink('settings/spam_protection'));
        }

        if ($type !== 'statuses' && $this->handlePost($type)) {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('Success'))
                ->defer();

            return new RedirectView($this->getLink('settings/' . $type));
        }

        return match ($type) {
            self::TYPE_STATUSES => $this->statusesAction($id),
            self::TYPE_LICENSE => $this->licenseAction(),
            self::TYPE_FORMATTING_TEMPLATES => $this->formattingTemplatesAction(),
            self::TYPE_EMAIL_TEMPLATES => $this->emailTemplatesAction(),
            self::TYPE_DEMO_TEMPLATES => $this->demoTemplatesAction(),
            self::TYPE_PERMISSIONS => $this->permissionsAction(),
            self::TYPE_SPAM_PROTECTION => $this->spamProtectionAction(),
            default => $this->generalAction(),
        };
    }

    /**
     * @param null|string|int $id
     *
     * @return View
     * @throws FreeformException
     */
    public function statusesAction(null|string|int $id = null)
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings();

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
    private function licenseAction(): RedirectView|CpView
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings();

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
    public function permissionDenied(): CpView
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
    private function generalAction(): CpView
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
	private function spamProtectionAction(): CpView
	{
		$settings = $this->getSettings();

        $sections = [
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
        ];

        if (FreeformHelper::isFreeformAtLeast('3.3.5')) {
            $sections[0][] = [
                'title'  => 'Spam Folder',
                'desc'   => 'When enabled, all submissions caught by spam protection measures will be flagged as spam and stored in the database, but available to manage in a separate menu inside Freeform.',
                'fields' => [
                    'spamFolderEnabled' => [
                        'type'  => 'yes_no',
                        'value' => $settings->isSpamFolderEnabled(),
                    ],
                ],
            ];
        }

        $sections[] = [
            [
                'title' => 'CAPTCHA Provider',
                'desc' => 'Choose one provider for visible CAPTCHA fields or automatic reCAPTCHA v3 protection. Add the CAPTCHA field to the final page of forms using a visible provider.',
                'fields' => [
                    'captchaProvider' => [
                        'type' => 'select',
                        'value' => $settings->getCaptchaProvider(),
                        'choices' => [
                            SettingsModel::CAPTCHA_NONE => 'None',
                            SettingsModel::CAPTCHA_RECAPTCHA => 'reCAPTCHA',
                            SettingsModel::CAPTCHA_TURNSTILE => 'Cloudflare Turnstile',
                            SettingsModel::CAPTCHA_HCAPTCHA => 'hCaptcha',
                        ],
                        'group_toggle' => [
                            SettingsModel::CAPTCHA_RECAPTCHA => 'recaptchaOptions',
                            SettingsModel::CAPTCHA_TURNSTILE => 'turnstileOptions',
                            SettingsModel::CAPTCHA_HCAPTCHA => 'hcaptchaOptions',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'reCAPTCHA Type',
                'group' => 'recaptchaOptions',
                'fields' => [
                    'recaptchaType' => [
                        'type' => 'select',
                        'value' => $settings->getRecaptchaType(),
                        'choices' => [
                            'v2-checkbox' => 'Challenge - Checkbox (v2)',
                            'v3' => 'Score Based (v3)',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'reCAPTCHA Site Key',
                'group' => 'recaptchaOptions',
                'fields' => ['recaptchaKey' => ['type' => 'text', 'value' => $settings->getRecaptchaKey()]],
            ],
            [
                'title' => 'reCAPTCHA Secret Key',
                'group' => 'recaptchaOptions',
                'fields' => ['recaptchaSecret' => ['type' => 'text', 'value' => $settings->getRecaptchaSecret()]],
            ],
            [
                'title' => 'reCAPTCHA Score Threshold',
                'desc' => 'Used only with score based reCAPTCHA (v3).',
                'group' => 'recaptchaOptions',
                'fields' => [
                    'recaptchaScoreThreshold' => [
                        'type' => 'select',
                        'value' => $settings->getRecaptchaScoreThreshold() ?: '0.5',
                        'choices' => array_combine(
                            array_map(static fn ($n) => sprintf('%.1f', $n / 10), range(0, 10)),
                            array_map(static fn ($n) => sprintf('%.1f', $n / 10), range(0, 10))
                        ),
                    ],
                ],
            ],
            [
                'title' => 'Turnstile Site Key',
                'group' => 'turnstileOptions',
                'fields' => ['turnstileKey' => ['type' => 'text', 'value' => $settings->getTurnstileKey()]],
            ],
            [
                'title' => 'Turnstile Secret Key',
                'group' => 'turnstileOptions',
                'fields' => ['turnstileSecret' => ['type' => 'text', 'value' => $settings->getTurnstileSecret()]],
            ],
            [
                'title' => 'hCaptcha Site Key',
                'group' => 'hcaptchaOptions',
                'fields' => ['hcaptchaKey' => ['type' => 'text', 'value' => $settings->getHcaptchaKey()]],
            ],
            [
                'title' => 'hCaptcha Secret Key',
                'group' => 'hcaptchaOptions',
                'fields' => ['hcaptchaSecret' => ['type' => 'text', 'value' => $settings->getHcaptchaSecret()]],
            ],
        ];

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
					'sections'              => $sections,
                ]
			);

		return $view;
	}
    /**
     * @return CpView
     */
    private function permissionsAction(): CpView
    {
        $version = FreeformHelper::getVersion();

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
                'title'  => 'Manage Forms',
                'desc'   => 'Choose which roles can manage Forms.',
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
                'desc'   => 'Choose which roles have access to Submissions.',
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
                'desc'   => 'Choose which roles can manage Submissions.',
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
                'desc'   => 'Choose which roles have access to the Field Manager.',
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
                'desc'   => 'Choose which roles have access to Notifications.',
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
                'desc'   => 'Choose which roles have access to Export.',
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
                'desc'   => 'Choose which roles have access to Settings.',
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
                'desc'   => 'Choose which roles have access to Integrations.',
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
                'desc'   => 'Choose which roles have access to Error logs.',
                'fields' => [
                    'logsPermissions' => [
                        'type'    => 'checkbox',
                        'value'   => $permissionsModel->logsPermissions,
                        'choices' => $memberRoleChoices,
                    ],
                ],
            ],
        ];

        $sections = [...$sections, ...$additionalSections];

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
    private function formattingTemplatesAction(): CpView
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
    private function emailTemplatesAction(): CpView
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
    private function demoTemplatesAction(): RedirectView|CpView
    {
        $controller = new DemoTemplatesController();

        return $controller->index();
    }

    /**
     * Handles a POST request and returns one of the following
     * TRUE  - if it was handled
     * FALSE - if there were errors
     * NULL  - if nothing was posted
     *
     * @return bool
     */
    private function handlePost(string $type): ?bool
    {
        if ($type == self::TYPE_PERMISSIONS) {
            $settings = $this->getPermissionsModel();
        } else {
            $settings = $this->getSettings();
        }

        if (!empty($_POST) && !isset($_POST['prefix'])) {
            if ($type === self::TYPE_SPAM_PROTECTION && isset($_POST['captchaProvider']) && !in_array(
                $_POST['captchaProvider'],
                [SettingsModel::CAPTCHA_NONE, SettingsModel::CAPTCHA_RECAPTCHA, SettingsModel::CAPTCHA_TURNSTILE, SettingsModel::CAPTCHA_HCAPTCHA],
                true
            )) {
                return false;
            }
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
     * @return string
     */
    private function getActionUrl(string $method): string
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
