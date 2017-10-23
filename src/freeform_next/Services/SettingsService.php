<?php

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\DataObjects\FormTemplate;
use Solspace\Addons\FreeformNext\Library\Session\DbSession;
use Solspace\Addons\FreeformNext\Library\Session\EESession;
use Solspace\Addons\FreeformNext\Library\Session\SessionInterface;
use Solspace\Addons\FreeformNext\Model\SettingsModel;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;

class SettingsService
{
    /** @var SettingsModel */
    private static $settingsModel;

    /**
     * @return string
     */
    public function getFormTemplateDirectory()
    {
        return $this->getSettingsModel()->getFormattingTemplatePath();
    }

    /**
     * @return string
     */
    public function getSolspaceFormTemplateDirectory()
    {
        return realpath(PATH_THIRD . '/freeform_next/Templates/form');
    }

    /**
     * Mark the tutorial as finished
     */
    public function finishTutorial()
    {
        $settings               = $this->getSettingsModel();
        $settings->showTutorial = false;
        $settings->save();

        return true;
    }

    /**
     * @return FormTemplate[]
     */
    public function getSolspaceFormTemplates()
    {
        $templateDirectoryPath = $this->getSolspaceFormTemplateDirectory();

        if (!$templateDirectoryPath) {
            return [];
        }

        $files = [];
        $dir = new \DirectoryIterator($templateDirectoryPath);
        foreach ($dir as $fileInfo) {
            if (!$fileInfo->isDot() && !$fileInfo->isDir() && $fileInfo->getFilename() !== '.htaccess') {
                $files[] = new FormTemplate($templateDirectoryPath . '/' . $fileInfo->getFilename());
            }
        }

        return $files;
    }

    /**
     * @return FormTemplate[]
     */
    public function getCustomFormTemplates()
    {
        $templates = [];
        foreach ($this->getSettingsModel()->listTemplatesInFormTemplateDirectory() as $path => $name) {
            $templates[] = new FormTemplate($path);
        }

        return $templates;
    }

    /**
     * @return bool
     */
    public function isDbEmailTemplateStorage()
    {
        return $this->getSettingsModel()->isDbEmailTemplateStorage();
    }

    /**
     * @return bool
     */
    public function isDatabaseSessionStorage()
    {
        return $this->getSettingsModel()->isDatabaseSessionStorage();
    }

    /**
     * @return bool
     */
    public function isDefaultTemplates()
    {
        return $this->getSettingsModel()->isDefaultTemplates();
    }

    /**
     * @return bool
     */
    public function isFormSubmitDisable()
    {
        return $this->getSettingsModel()->isFormSubmitDisable();
    }

    /**
     * @return SessionInterface
     */
    public function getSessionStorageImplementation()
    {
        if ($this->isDatabaseSessionStorage()) {
            return new DbSession();
        }

        return new EESession();
    }

    /**
     * @return SettingsModel
     */
    public function getSettingsModel()
    {
        if (null === self::$settingsModel) {
            self::$settingsModel = SettingsRepository::getInstance()->getOrCreate();
        }

        return self::$settingsModel;
    }

    /**
     * Remove all stale stored session data entries
     */
    public function cleanUpDatabaseSessionData()
    {
        $date = new \DateTime('-180 minutes');

        ee()->db
            ->delete(
                'exp_freeform_next_session_data',
                ['dateCreated <' => $date->format('Y-m-d H:i:s')]
            );
    }
}
