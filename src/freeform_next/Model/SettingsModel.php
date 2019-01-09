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

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @property int    $id
 * @property int    $siteId
 * @property bool   $spamProtectionEnabled
 * @property bool   $spamBlockLikeSuccessfulPost
 * @property bool   $showTutorial
 * @property string $fieldDisplayOrder
 * @property string $formattingTemplatePath
 * @property string $notificationTemplatePath
 * @property string $notificationCreationMethod
 * @property string $license
 * @property string $sessionStorage
 * @property bool   $defaultTemplates
 * @property bool   $removeNewlines
 * @property bool   $formSubmitDisable
 * @property bool   $autoScrollToErrors
 */
class SettingsModel extends Model
{
    const MODEL = 'freeform_next:SettingsModel';
    const TABLE = 'freeform_next_settings';

    const NOTIFICATION_CREATION_METHOD_DATABASE = 'db';
    const NOTIFICATION_CREATION_METHOD_TEMPLATE = 'template';

    const FIELD_DISPLAY_ORDER_TYPE = 'type';
    const FIELD_DISPLAY_ORDER_NAME = 'name';

    const DEFAULT_SPAM_PROTECTION_ENABLED         = true;
    const DEFAULT_SPAM_BLOCK_LIKE_SUCCESSFUL_POST = false;
    const DEFAULT_SHOW_TUTORIAL                   = true;
    const DEFAULT_FIELD_DISPLAY_ORDER             = self::FIELD_DISPLAY_ORDER_TYPE;
    const DEFAULT_FORMATTING_TEMPLATE_PATH        = null;
    const DEFAULT_NOTIFICATION_TEMPLATE_PATH      = null;
    const DEFAULT_NOTIFICATION_CREATION_METHOD    = self::NOTIFICATION_CREATION_METHOD_DATABASE;
    const DEFAULT_LICENSE                         = null;
    const DEFAULT_DEFAULT_TEMPLATES               = true;
    const DEFAULT_REMOVE_NEWLINES                 = false;
    const DEFAULT_FORM_SUBMIT_DISABLE             = true;
    const DEFAULT_AUTO_SCROLL_TO_ERRORS           = true;

    const SESSION_STORAGE_SESSION  = 'session';
    const SESSION_STORAGE_DATABASE = 'db';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $spamProtectionEnabled;
    protected $spamBlockLikeSuccessfulPost;
    protected $showTutorial;
    protected $fieldDisplayOrder;
    protected $formattingTemplatePath;
    protected $notificationTemplatePath;
    protected $notificationCreationMethod;
    protected $license;
    protected $sessionStorage;
    protected $defaultTemplates;
    protected $removeNewlines;
    protected $formSubmitDisable;
    protected $recaptchaEnabled;
    protected $recaptchaKey;
    protected $recaptchaSecret;
    protected $autoScrollToErrors;

    /**
     * Creates a Settings Model
     *
     * @return SettingsModel
     */
    public static function create()
    {
        /** @var SettingsModel $settings */
        $settings = ee('Model')->make(
            self::MODEL,
            [
                'siteId'                      => ee()->config->item('site_id'),
                'spamProtectionEnabled'       => self::DEFAULT_SPAM_PROTECTION_ENABLED,
                'spamBlockLikeSuccessfulPost' => self::DEFAULT_SPAM_BLOCK_LIKE_SUCCESSFUL_POST,
                'showTutorial'                => self::DEFAULT_SHOW_TUTORIAL,
                'fieldDisplayOrder'           => self::DEFAULT_FIELD_DISPLAY_ORDER,
                'formattingTemplatePath'      => self::DEFAULT_FORMATTING_TEMPLATE_PATH,
                'notificationTemplatePath'    => self::DEFAULT_NOTIFICATION_TEMPLATE_PATH,
                'notificationCreationMethod'  => self::DEFAULT_NOTIFICATION_CREATION_METHOD,
                'license'                     => self::DEFAULT_LICENSE,
                'sessionStorage'              => self::SESSION_STORAGE_SESSION,
                'defaultTemplates'            => self::DEFAULT_DEFAULT_TEMPLATES,
                'removeNewlines'              => self::DEFAULT_REMOVE_NEWLINES,
                'formSubmitDisable'           => self::DEFAULT_FORM_SUBMIT_DISABLE,
                'recaptchaEnabled'            => false,
                'recaptchaKey'                => null,
                'recaptchaSecret'             => null,
                'autoScrollToErrors'          => self::DEFAULT_AUTO_SCROLL_TO_ERRORS,
            ]
        );

        return $settings;
    }

    /**
     * If a form template directory has been set and it exists - return its absolute path
     *
     * @return null|string
     */
    public function getAbsoluteFormTemplateDirectory()
    {
        if ($this->formattingTemplatePath) {
            $absolutePath = $this->getAbsolutePath($this->formattingTemplatePath);

            return file_exists($absolutePath) ? $absolutePath : null;
        }

        return null;
    }

    /**
     * If an email template directory has been set and it exists - return its absolute path
     *
     * @return null|string
     */
    public function getAbsoluteEmailTemplateDirectory()
    {
        if ($this->notificationTemplatePath) {
            $absolutePath = $this->getAbsolutePath($this->notificationTemplatePath);

            return file_exists($absolutePath) ? $absolutePath : null;
        }

        return null;
    }

    /**
     * Gets the demo template content
     *
     * @param string $name
     *
     * @return string
     * @throws FreeformException
     */
    public function getDemoTemplateContent($name = 'flexbox')
    {
        $path = PATH_THIRD . "freeform_next/Templates/form/$name.html";
        if (!file_exists($path)) {
            throw new FreeformException(lang('Could not get demo template content. Please contact Solspace.'));
        }

        return file_get_contents($path);
    }

    /**
     * @return array|bool
     */
    public function listTemplatesInFormTemplateDirectory()
    {
        $templateDirectoryPath = $this->getAbsoluteFormTemplateDirectory();

        if (!$templateDirectoryPath) {
            return [];
        }

        $fs = new Finder();
        /** @var SplFileInfo[] $fileIterator */
        $fileIterator = $fs->files()->in($templateDirectoryPath)->name('*.html');
        $files        = [];

        foreach ($fileIterator as $file) {
            $files[$file->getRealPath()] = $file->getBasename();
        }

        return $files;
    }

    /**
     * @return array|bool
     */
    public function listTemplatesInEmailTemplateDirectory()
    {
        $templateDirectoryPath = $this->getAbsoluteEmailTemplateDirectory();

        if (!$templateDirectoryPath) {
            return [];
        }

        $fs = new Finder();
        /** @var SplFileInfo[] $fileIterator */
        $fileIterator = $fs->files()->in($templateDirectoryPath)->name('*.html');
        $files        = [];

        foreach ($fileIterator as $file) {
            $files[$file->getRealPath()] = $file->getBasename();
        }

        return $files;
    }

    /**
     * Gets the default email template content
     *
     * @return string
     * @throws FreeformException
     */
    public function getEmailTemplateContent()
    {
        $path = PATH_THIRD . 'freeform_next/Templates/notifications/default.html';
        if (!file_exists($path)) {
            throw new FreeformException(
                lang('Could not get email template content. Please contact Solspace.')
            );
        }

        return file_get_contents($path);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @return int
     */
    public function getSiteId()
    {
        return (int) $this->siteId;
    }

    /**
     * @return bool
     */
    public function isSpamProtectionEnabled()
    {
        return (bool) $this->spamProtectionEnabled;
    }

    /**
     * @return bool
     */
    public function isSpamBlockLikeSuccessfulPost()
    {
        return (bool) $this->spamBlockLikeSuccessfulPost;
    }

    /**
     * @return bool
     */
    public function isShowTutorial()
    {
        return (bool) $this->showTutorial;
    }

    /**
     * @return string
     */
    public function getFieldDisplayOrder()
    {
        return $this->fieldDisplayOrder;
    }

    /**
     * @return string
     */
    public function getFormattingTemplatePath()
    {
        return $this->formattingTemplatePath;
    }

    /**
     * @return string
     */
    public function getNotificationTemplatePath()
    {
        return $this->notificationTemplatePath;
    }

    /**
     * @return string
     */
    public function getNotificationCreationMethod()
    {
        return $this->notificationCreationMethod;
    }

    /**
     * @return bool
     */
    public function isDbEmailTemplateStorage()
    {
        return $this->notificationCreationMethod === self::NOTIFICATION_CREATION_METHOD_DATABASE;
    }

    /**
     * @return bool
     */
    public function isDatabaseSessionStorage()
    {
        return $this->sessionStorage === self::SESSION_STORAGE_DATABASE;
    }

    /**
     * @return bool
     */
    public function isDefaultTemplates()
    {
        return (bool) $this->defaultTemplates;
    }

    /**
     * @return bool
     */
    public function isFormSubmitDisable()
    {
        return (bool) $this->formSubmitDisable;
    }

    /**
     * @return bool
     */
    public function isAutoScrollToErrors()
    {
        return (bool) $this->autoScrollToErrors;
    }

    /**
     * @return mixed
     */
    public function isRecaptchaEnabled()
    {
        return (bool) $this->recaptchaEnabled;
    }

    /**
     * @return mixed
     */
    public function getRecaptchaKey()
    {
        return $this->recaptchaKey;
    }

    /**
     * @return mixed
     */
    public function getRecaptchaSecret()
    {
        return $this->recaptchaSecret;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getAbsolutePath($path)
    {
        $isAbsolute = $this->isFolderAbsolute($path);

        return $isAbsolute ? $path : (PATH_TMPL . $path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function isFolderAbsolute($path)
    {
        return preg_match("/^(?:\/|\\\\|\w\:\\\\).*$/", $path);
    }
}
