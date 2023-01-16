<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Composer;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Objects\ComposerState;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\FieldModel;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Model\StatusModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Services\StatusesService;
use Solspace\Addons\FreeformNext\Services\SubmissionsService;
use Symfony\Component\Config\Definition\Exception\Exception;

class NextFormStatusHelper
{
    const STRICT_MODE = true;

    /** @var array */
    public $errors;

    public function saveStatus($classicStatusHandle, $classicStatusName)
    {
        if (StatusRepository::getInstance()->getStatusByHandle($classicStatusHandle)) {
            return false;
        }

        $model = StatusModel::create();
        $isNew = !$model->id;

        $name    = $classicStatusName;
        $handle  = $classicStatusHandle;
        $color   = '#ffffff';
        $default = false;

        $model->set(['name' => $name]);
        $model->handle    = $handle;
        $model->color     = $color;
        $model->isDefault = $default;

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_STATUS_BEFORE_SAVE, $model, $isNew)) {
            return $model;
        }

        $model->save();

        ExtensionHelper::call(ExtensionHelper::HOOK_STATUS_AFTER_SAVE, $model, $isNew);

        if ($model->isDefault) {
            ee()
                ->db
                ->update(
                    StatusModel::TABLE,
                    ['isDefault' => 0],
                    ['id !=' => $model->id]
                );
        } else {
            $this->updateDefaults();
        }

        return true;
    }

    /**
     * Sets the isDefault to TRUE for the first entry found if no isDefault is set
     */
    private function updateDefaults()
    {
        $hasDefault = ee()
            ->db
            ->where('isDefault', true)
            ->get(StatusModel::TABLE)
            ->num_rows();

        if (!$hasDefault) {
            ee()
                ->db
                ->limit(1)
                ->update(
                    StatusModel::TABLE,
                    ['isDefault' => true]
                );
        }
    }
}
