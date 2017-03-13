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

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

/**
 * Class FieldModel
 *
 * @property int    $id
 * @property int    $siteId
 * @property bool   $spamProtectionEnabled
 * @property bool   $showTutorial
 * @property string $fieldDisplayOrder
 * @property string $formattingTemplatePath
 * @property string $notificationTemplatePath
 * @property string $notificationCreationMethod
 */
class SettingsModel extends Model
{
    const MODEL = 'freeform_next:SettingsModel';
    const TABLE = 'freeform_next_settings';

    const NOTIFICATION_CREATION_METHOD_DATABASE = 'db';
    const NOTIFICATION_CREATION_METHOD_TEMPLATE = 'template';

    const FIELD_DISPLAY_ORDER_TYPE = 'type';
    const FIELD_DISPLAY_ORDER_NAME = 'name';

    const DEFAULT_SPAM_PROTECTION_ENABLED      = true;
    const DEFAULT_SHOW_TUTORIAL                = true;
    const DEFAULT_FIELD_DISPLAY_ORDER          = self::FIELD_DISPLAY_ORDER_TYPE;
    const DEFAULT_FORMATTING_TEMPLATE_PATH     = null;
    const DEFAULT_NOTIFICATION_TEMPLATE_PATH   = null;
    const DEFAULT_NOTIFICATION_CREATION_METHOD = self::NOTIFICATION_CREATION_METHOD_DATABASE;

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $spamProtectionEnabled;
    protected $showTutorial;
    protected $fieldDisplayOrder;
    protected $formattingTemplatePath;
    protected $notificationTemplatePath;
    protected $notificationCreationMethod;

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
                'siteId'                     => ee()->config->item('site_id'),
                'spamProtectionEnabled'      => self::DEFAULT_SPAM_PROTECTION_ENABLED,
                'showTutorial'               => self::DEFAULT_SHOW_TUTORIAL,
                'fieldDisplayOrder'          => self::DEFAULT_FIELD_DISPLAY_ORDER,
                'formattingTemplatePath'     => self::DEFAULT_FORMATTING_TEMPLATE_PATH,
                'notificationTemplatePath'   => self::DEFAULT_NOTIFICATION_TEMPLATE_PATH,
                'notificationCreationMethod' => self::DEFAULT_NOTIFICATION_CREATION_METHOD,
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

        $files = [];
        $dir = new \DirectoryIterator($templateDirectoryPath);
        foreach ($dir as $fileInfo) {
            if (!$fileInfo->isDot() && !$fileInfo->isDir() && $fileInfo->getFilename() !== '.htaccess') {
                $files[$fileInfo->getPathname()] = $fileInfo->getBasename();
            }
        }

        return $files;
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
