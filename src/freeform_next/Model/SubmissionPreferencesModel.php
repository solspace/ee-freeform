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

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionPreferenceSetting;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

/**
 * @property int    $id
 * @property int    $siteId
 * @property int    $memberId
 * @property int    $formId
 * @property string $settings
 */
class SubmissionPreferencesModel extends Model
{
    const MODEL = 'freeform_next:SubmissionPreferencesModel';
    const TABLE = 'freeform_next_submission_preferences';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $memberId;
    protected $formId;
    protected $settings;

    /** @var SubmissionPreferenceSetting[] */
    private $layout;

    /** @var array */
    protected static $_typed_columns = [
        'settings' => 'json',
    ];

    /**
     * Creates a Field object with default settings
     *
     * @param Form $form
     * @param int  $memberId
     *
     * @return SubmissionPreferencesModel
     * @throws FreeformException
     */
    public static function create(Form $form, $memberId)
    {
        if (!$memberId) {
            throw new FreeformException('No member ID supplied to submission preferences');
        }

        /** @var SubmissionPreferencesModel $model */
        $model = ee('Model')->make(
            self::MODEL,
            [
                'siteId'   => ee()->config->item('site_id'),
                'formId'   => $form->getId(),
                'memberId' => $memberId,
            ]
        );

        return $model;
    }

    /**
     * @return FormModel
     */
    public function getForm()
    {
        return FormRepository::getInstance()->getFormById($this->formId);
    }

    /**
     * @return SubmissionPreferenceSetting[]
     */
    public function getLayout()
    {
        if (null === $this->layout) {
            /** @var array $settings */
            $settings   = $this->settings;
            $form       = $this->getForm()->getForm();
            $formLayout = $form->getLayout();

            $layout = $usedIds = [];
            $hasId  = $hasTitle = $hasCreated = $hasStatus = false;

            if (null !== $settings) {
                foreach ($settings as $item) {
                    if (is_numeric($item['id'])) {
                        try {
                            $field = $formLayout->getFieldById($item['id']);
                        } catch (FreeformException $e) {
                            continue;
                        }

                        if ($field instanceof NoStorageInterface) {
                            continue;
                        }

                        $layout[]  = SubmissionPreferenceSetting::createFromField($field, $item['checked']);
                        $usedIds[] = $field->getId();
                    } else {
                        $layout[] = SubmissionPreferenceSetting::createFromArray($item);
                        if ($item['id'] === 'id') {
                            $hasId = true;
                        }

                        if ($item['id'] === 'title') {
                            $hasTitle = true;
                        }

                        if ($item['id'] === 'dateCreated') {
                            $hasCreated = true;
                        }

                        if ($item['id'] === 'statusName') {
                            $hasStatus = true;
                        }
                    }
                }
            }

            if (!$hasStatus) {
                array_unshift(
                    $layout,
                    new SubmissionPreferenceSetting('statusName', 'statusName', 'Status', true)
                );
            }

            if (!$hasCreated) {
                array_unshift(
                    $layout,
                    new SubmissionPreferenceSetting('dateCreated', 'dateCreated', 'Date Created', true)
                );
            }

            if (!$hasTitle) {
                array_unshift(
                    $layout,
                    new SubmissionPreferenceSetting('title', 'title', 'Title', true)
                );
            }

            if (!$hasId) {
                array_unshift(
                    $layout,
                    new SubmissionPreferenceSetting('id', 'id', 'ID', true)
                );
            }

            foreach ($formLayout->getFields() as $field) {
                if ($field instanceof NoStorageInterface || in_array($field->getId(), $usedIds, true)) {
                    continue;
                }

                $layout[] = SubmissionPreferenceSetting::createFromField($field, true);
            }

            $this->layout = $layout;
        }

        return $this->layout;
    }

    /**
     * @param string $columnName
     *
     * @return null|string
     */
    public function getDatabaseColumnName($columnName)
    {
        $layout = $this->getLayout();

        foreach ($layout as $item) {
            if ($item->getHandle() === $columnName) {
                if (is_numeric($item->getId())) {
                    return SubmissionModel::getFieldColumnName($item->getId());
                }

                return $item->getId();
            }
        }

        return null;
    }
}
