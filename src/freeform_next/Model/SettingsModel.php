<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
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
 * @property bool   $freeformHoneypotEnhancement
 * @property bool   $spamBlockLikeSuccessfulPost
 * @property bool   $spamFolderEnabled
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
 * @property bool   $recaptchaEnabled
 * @property bool   $recaptchaType
 * @property bool   $recaptchaKey
 * @property bool   $recaptchaSecret
 * @property bool   $recaptchaScoreThreshold
 * @property string $captchaProvider
 * @property string $turnstileKey
 * @property string $turnstileSecret
 * @property string $turnstileTheme
 * @property string $turnstileSize
 * @property string $hcaptchaKey
 * @property string $hcaptchaSecret
 * @property string $hcaptchaTheme
 * @property string $hcaptchaSize
 */
class SettingsModel extends Model
{
    public const MODEL = 'freeform_next:SettingsModel';
    public const TABLE = 'freeform_next_settings';

    public const NOTIFICATION_CREATION_METHOD_DATABASE = 'db';
    public const NOTIFICATION_CREATION_METHOD_TEMPLATE = 'template';

    public const FIELD_DISPLAY_ORDER_TYPE = 'type';
    public const FIELD_DISPLAY_ORDER_NAME = 'name';

    public const DEFAULT_SPAM_PROTECTION_ENABLED         = true;
    public const DEFAULT_SPAM_BLOCK_LIKE_SUCCESSFUL_POST = false;
    public const DEFAULT_SPAM_FOLDER_ENABLED             = true;
    public const DEFAULT_SHOW_TUTORIAL                   = true;
    public const DEFAULT_FIELD_DISPLAY_ORDER             = self::FIELD_DISPLAY_ORDER_TYPE;
    public const DEFAULT_FORMATTING_TEMPLATE_PATH        = null;
    public const DEFAULT_NOTIFICATION_TEMPLATE_PATH      = null;
    public const DEFAULT_NOTIFICATION_CREATION_METHOD    = self::NOTIFICATION_CREATION_METHOD_DATABASE;
    public const DEFAULT_LICENSE                         = null;
    public const DEFAULT_DEFAULT_TEMPLATES               = true;
    public const DEFAULT_REMOVE_NEWLINES                 = false;
    public const DEFAULT_FORM_SUBMIT_DISABLE             = true;
    public const DEFAULT_AUTO_SCROLL_TO_ERRORS           = true;
    public const DEFAULT_RECAPTCHA_ENABLED               = false;
    public const DEFAULT_RECAPTCHA_TYPE                  = null;
    public const DEFAULT_RECAPTCHA_KEY                   = null;
    public const DEFAULT_RECAPTCHA_SECRET                = null;
    public const DEFAULT_RECAPTCHA_SCORE_THRESHOLD       = '0.5';
    public const CAPTCHA_NONE = 'none';
    public const CAPTCHA_RECAPTCHA = 'recaptcha';
    public const CAPTCHA_TURNSTILE = 'turnstile';
    public const CAPTCHA_HCAPTCHA = 'hcaptcha';

    public const SESSION_STORAGE_SESSION  = 'session';
    public const SESSION_STORAGE_DATABASE = 'db';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $spamProtectionEnabled;
    protected $freeformHoneypotEnhancement;
    protected $spamBlockLikeSuccessfulPost;
    protected $spamFolderEnabled;
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
    protected $recaptchaType;
    protected $recaptchaKey;
    protected $recaptchaSecret;
    protected $recaptchaScoreThreshold;
    protected $captchaProvider;
    protected $turnstileKey;
    protected $turnstileSecret;
    protected $turnstileTheme;
    protected $turnstileSize;
    protected $hcaptchaKey;
    protected $hcaptchaSecret;
    protected $hcaptchaTheme;
    protected $hcaptchaSize;
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
                'spamFolderEnabled'           => self::DEFAULT_SPAM_FOLDER_ENABLED,
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
                'recaptchaEnabled'            => self::DEFAULT_RECAPTCHA_ENABLED,
                'recaptchaType'               => self::DEFAULT_RECAPTCHA_TYPE,
                'recaptchaKey'                => self::DEFAULT_RECAPTCHA_KEY,
                'recaptchaSecret'             => self::DEFAULT_RECAPTCHA_SECRET,
                'recaptchaScoreThreshold'     => self::DEFAULT_RECAPTCHA_SCORE_THRESHOLD,
                'captchaProvider'             => null,
                'turnstileKey'                => null,
                'turnstileSecret'             => null,
                'turnstileTheme'              => 'auto',
                'turnstileSize'               => 'normal',
                'hcaptchaKey'                 => null,
                'hcaptchaSecret'              => null,
                'hcaptchaTheme'               => 'light',
                'hcaptchaSize'                => 'normal',
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
    public function getDemoTemplateContent($name = 'flexbox'): string|bool
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
    public function getEmailTemplateContent(): string|bool
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
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return (int) $this->siteId;
    }

    /**
     * @return bool
     */
    public function isSpamProtectionEnabled(): bool
    {
        return (bool) $this->spamProtectionEnabled;
    }

	/**
	 * @return bool
	 */
	public function isFreeformHoneypotEnhanced(): bool
	{
		return (bool) $this->freeformHoneypotEnhancement;
	}

    /**
     * @return bool
     */
    public function isSpamBlockLikeSuccessfulPost(): bool
    {
        return (bool) $this->spamBlockLikeSuccessfulPost;
    }

    /**
     * @return bool
     */
    public function isSpamFolderEnabled(): bool
    {
        return (bool) $this->spamFolderEnabled;
    }

    /**
     * @return bool
     */
    public function isShowTutorial(): bool
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
    public function isDbEmailTemplateStorage(): bool
    {
        return $this->notificationCreationMethod === self::NOTIFICATION_CREATION_METHOD_DATABASE;
    }

    /**
     * @return bool
     */
    public function isDatabaseSessionStorage(): bool
    {
        return $this->sessionStorage === self::SESSION_STORAGE_DATABASE;
    }

    /**
     * @return bool
     */
    public function isDefaultTemplates(): bool
    {
        return (bool) $this->defaultTemplates;
    }

    /**
     * @return bool
     */
    public function isFormSubmitDisable(): bool
    {
        return (bool) $this->formSubmitDisable;
    }

    /**
     * @return bool
     */
    public function isAutoScrollToErrors(): bool
    {
        return (bool) $this->autoScrollToErrors;
    }

    /**
     * @return mixed
     */
    public function isRecaptchaEnabled(): bool
    {
        return $this->getCaptchaProvider() === self::CAPTCHA_RECAPTCHA;
    }

    public function getCaptchaProvider(): string
    {
        // An unset provider preserves the pre-v4 reCAPTCHA switch on upgrade.
        return $this->captchaProvider ?: ($this->recaptchaEnabled ? self::CAPTCHA_RECAPTCHA : self::CAPTCHA_NONE);
    }

    public function getCaptchaSiteKey(): ?string
    {
        return match ($this->getCaptchaProvider()) {
            self::CAPTCHA_RECAPTCHA => $this->recaptchaKey,
            self::CAPTCHA_TURNSTILE => $this->turnstileKey,
            self::CAPTCHA_HCAPTCHA => $this->hcaptchaKey,
            default => null,
        };
    }

    public function getCaptchaSecret(): ?string
    {
        return match ($this->getCaptchaProvider()) {
            self::CAPTCHA_RECAPTCHA => $this->recaptchaSecret,
            self::CAPTCHA_TURNSTILE => $this->turnstileSecret,
            self::CAPTCHA_HCAPTCHA => $this->hcaptchaSecret,
            default => null,
        };
    }

    public function getTurnstileKey(): ?string { return $this->turnstileKey; }
    public function getTurnstileSecret(): ?string { return $this->turnstileSecret; }
    public function getTurnstileTheme(): string { return in_array($this->turnstileTheme, ['auto', 'light', 'dark'], true) ? $this->turnstileTheme : 'auto'; }
    public function getTurnstileSize(): string { return in_array($this->turnstileSize, ['normal', 'flexible', 'compact'], true) ? $this->turnstileSize : 'normal'; }
    public function getHcaptchaKey(): ?string { return $this->hcaptchaKey; }
    public function getHcaptchaSecret(): ?string { return $this->hcaptchaSecret; }
    public function getHcaptchaTheme(): string { return in_array($this->hcaptchaTheme, ['light', 'dark'], true) ? $this->hcaptchaTheme : 'light'; }
    public function getHcaptchaSize(): string { return in_array($this->hcaptchaSize, ['normal', 'compact'], true) ? $this->hcaptchaSize : 'normal'; }

    /**
     * @return mixed
     */
    public function getRecaptchaType()
    {
        return $this->recaptchaType;
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
     * @return mixed
     */
    public function getRecaptchaScoreThreshold()
    {
        return $this->recaptchaScoreThreshold;
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
    private function isFolderAbsolute($path): int|bool
    {
        return preg_match("/^(?:\/|\\\\|\w\:\\\\).*$/", $path);
    }
}
