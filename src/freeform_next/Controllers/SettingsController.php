<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Model\SettingsModel;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Utilities\AddonInfo;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;
use Stringy\Stringy;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SettingsController extends Controller
{
    const TYPE_GENERAL              = 'general';
    const TYPE_FORMATTING_TEMPLATES = 'formatting_templates';
    const TYPE_EMAIL_TEMPLATES      = 'email_templates';
    const TYPE_STATUSES             = 'statuses';
    const TYPE_DEMO_TEMPLATES       = 'demo_templates';

    /** @var array */
    private static $allowedTypes = [
        self::TYPE_GENERAL,
        self::TYPE_FORMATTING_TEMPLATES,
        self::TYPE_EMAIL_TEMPLATES,
        self::TYPE_STATUSES,
        self::TYPE_DEMO_TEMPLATES,
    ];

    /**
     * @param string     $type
     * @param int|string $id
     *
     * @return View
     * @throws FreeformException
     */
    public function index($type, $id = null)
    {
        if (!in_array($type, self::$allowedTypes, true)) {
            throw new FreeformException('Page does not exist');
        }

        $this->handlePost();

        switch ($type) {
            case self::TYPE_FORMATTING_TEMPLATES:
                return $this->formattingTemplatesAction();

            case self::TYPE_EMAIL_TEMPLATES:
                return $this->emailTemplatesAction();

            case self::TYPE_STATUSES:
                return $this->statusesAction($id);

            case self::TYPE_DEMO_TEMPLATES:
                return $this->demoTemplatesAction();

            case self::TYPE_GENERAL:
            default:
                return $this->generalAction();
        }
    }

    /**
     * @return CpView
     */
    private function generalAction()
    {
        $settings = $this->getSettings();

        $view = new CpView('settings/common', []);
        $view->setHeading(lang('General'));
        $view->setTemplateVariables(
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
                        [
                            'title'  => 'Show Composer Tutorial',
                            'desc'   => 'Enable this to show the interactive tutorial again in Composer. This setting disables again when the tutorial is completed or skipped.',
                            'fields' => [
                                'showTutorial' => [
                                    'type'  => 'yes_no',
                                    'value' => $settings->isShowTutorial(),
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

        $view = new CpView('settings/common', []);
        $view->setHeading(lang('Formatting Templates'));
        $view->setTemplateVariables(
            [
                'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
                'cp_page_title'         => $view->getHeading(),
                'save_btn_text'         => 'btn_save_settings',
                'save_btn_text_working' => 'btn_saving',
                'sections'              => [
                    [
                        [
                            'title'  => 'Directory Path',
                            'desc'   => 'Provide a relative path to the Craft root folder where your custom formatting templates directory is. This allows you to use Twig templates for your form formatting, and helps Composer locate these files to assign one of them to a form.',
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
            ]
        );

        return $view;
    }

    /**
     * @return CpView
     */
    private function emailTemplatesAction()
    {
        $settings = $this->getSettings();

        $view = new CpView('settings/common', []);
        $view->setHeading(lang('Email Templates'));

        $variables = [
            'base_url'              => ee('CP/URL', $this->getActionUrl(__FUNCTION__)),
            'cp_page_title'         => $view->getHeading(),
            'save_btn_text'         => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
            'sections'              => [
                [
                    [
                        'title'  => 'Directory Path',
                        'desc'   => 'Provide a relative path to the Craft root folder where your email templates directory is. This allows you to use Twig template files for your email formatting, and helps Composer locate these files when setting up notifications.',
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
        }

        $view->setTemplateVariables($variables);

        return $view;
    }

    private function demoTemplatesAction()
    {

    }

    /**
     * Handles a POST request and returns one of the following
     * TRUE  - if it was handled
     * FALSE - if there were errors
     * NULL  - if nothing was posted
     * @return bool
     */
    private function handlePost()
    {
        $settings = $this->getSettings();

        if (!empty($_POST)) {
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
}
