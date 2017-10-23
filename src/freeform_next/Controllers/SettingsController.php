<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\UrlHelper;
use Solspace\Addons\FreeformNext\Model\SettingsModel;
use Solspace\Addons\FreeformNext\Model\StatusModel;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Utilities\AddonInfo;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;
use Stringy\Stringy;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SettingsController extends Controller
{
    const TYPE_STATUSES             = 'statuses';
    const TYPE_LICENSE              = 'license';
    const TYPE_GENERAL              = 'general';
    const TYPE_FORMATTING_TEMPLATES = 'formatting_templates';
    const TYPE_EMAIL_TEMPLATES      = 'email_templates';
    const TYPE_DEMO_TEMPLATES       = 'demo_templates';

    /** @var array */
    private static $allowedTypes = [
        self::TYPE_STATUSES,
        self::TYPE_LICENSE,
        self::TYPE_GENERAL,
        self::TYPE_FORMATTING_TEMPLATES,
        self::TYPE_EMAIL_TEMPLATES,
        self::TYPE_DEMO_TEMPLATES,
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
        if (!in_array($type, self::$allowedTypes, true)) {
            throw new FreeformException('Page does not exist');
        }

        if ($type !== 'statuses' && $this->handlePost()) {
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
        if (strtolower($id) === 'delete') {
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
                                'desc'   => 'Enter your Freeform Next license key here.',
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
                            [
                                'title'  => 'Spam Protection',
                                'desc'   => 'Enable this to use Freeform\'s built in Javascript-based honeypot spam protection.',
                                'fields' => [
                                    'spamProtectionEnabled' => [
                                        'type'  => 'yes_no',
                                        'value' => $settings->isSpamProtectionEnabled(),
                                    ],
                                ],
                            ],
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
                                'title'  => 'Disable Submit Button on Form Submit',
                                'desc'   => 'Enable this to automatically disable the form\'s submit button when the user submits the form. This will prevent the form from double-submitting.',
                                'fields' => [
                                    'formSubmitDisable' => [
                                        'type'  => 'yes_no',
                                        'value' => $settings->isFormSubmitDisable() ? 'y' : 'n',
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
                        'desc'   => 'Provide a full path to the folder where your custom formatting templates directory is. This allows you to use HTML templates for your form formatting, and helps Composer locate these files to assign one of them to a form.',
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
                        'desc'   => 'Provide a full path to the folder where your email templates directory is. This allows you to use HTML template files for your email formatting, and helps Composer locate these files when setting up notifications.',
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
     * Handles a POST request and returns one of the following
     * TRUE  - if it was handled
     * FALSE - if there were errors
     * NULL  - if nothing was posted
     *
     * @return bool
     */
    private function handlePost()
    {
        $settings = $this->getSettings();

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
