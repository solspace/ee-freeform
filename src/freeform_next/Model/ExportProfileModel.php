<?php

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Database\Query;
use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

/**
 * Class Freeform_ExportProfileModel
 *
 * @property int    $id
 * @property int    $siteId
 * @property int    $formId
 * @property string $name
 * @property int    $limit
 * @property string $dateRange
 * @property array  $fields
 * @property array  $filters
 * @property array  $statuses
 */
class ExportProfileModel extends Model
{
    use TimestampableTrait;

    const MODEL = 'freeform_next:ExportProfileModel';
    const TABLE = 'freeform_next_export_profiles';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $name;
    protected $formId;
    protected $limit;
    protected $dateRange;
    protected $fields;
    protected $filters;
    protected $statuses;

    protected static $_typed_columns = [
        'fields'   => 'json',
        'filters'  => 'json',
        'statuses' => 'json',
    ];

    /**
     * @return array
     */
    public static function createValidationRules()
    {
        return [
            'name' => 'required',
        ];
    }

    /**
     * @param Form $form
     *
     * @return ExportProfileModel
     */
    public static function create(Form $form)
    {
        return ee('Model')->make(
            self::MODEL,
            [
                'siteId' => ee()->config->item('site_id'),
                'formId' => $form ? $form->getId() : null,
            ]
        );
    }

    /**
     * @return FormModel
     */
    public function getFormModel()
    {
        return FormRepository::getInstance()->getFormById($this->formId);
    }

    /**
     * @return int
     */
    public function getSubmissionCount()
    {
        $command = $this->buildCommand();

        $command->ar_select = [];
        $command->select('COUNT(*) as total');

        try {
            $results = $command->get()->result_array();

            if (isset($results[0])) {
                return (int) $results[0]['total'];
            }

            return 0;
        } catch (\Exception $e) {
            return 'Invalid Query';
        }
    }

    /**
     * @return array
     */
    public function getSubmissionData()
    {
        $command = $this->buildCommand();

        try {
            return $command->get()->result_array();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getDateRangeEnd()
    {
        if (empty($this->dateRange)) {
            return null;
        }

        if (is_numeric($this->dateRange)) {
            $time = new \DateTime("-{$this->dateRange} days");
            $time->setTime(0, 0, 0);

            return $time;
        }

        switch ($this->dateRange) {
            case 'today':
                return (new \DateTime('now'))->setTime(0, 0, 0);

            case 'yesterday':
                return (new \DateTime('-1 day'))->setTime(0, 0, 0);

            default:
                return new \DateTime('now');
        }
    }

    /**
     * @return array
     */
    public function getFieldSettings()
    {
        $form = $this->getFormModel()->getForm();

        $storedFieldIds = $fieldSettings = [];
        if (!empty($this->fields)) {
            foreach ($this->fields as $fieldId => $item) {
                $label     = $item['label'];
                $isChecked = (bool) $item['checked'];

                if (is_numeric($fieldId)) {
                    try {
                        $field = $form->getLayout()->getFieldById($fieldId);
                        $label = $field->getLabel();

                        $storedFieldIds[] = $field->getId();
                    } catch (FreeformException $e) {
                        continue;
                    }
                }

                $fieldSettings[$fieldId] = [
                    'label'   => $label,
                    'checked' => $isChecked,
                ];
            }
        }

        if (empty($fieldSettings)) {
            $fieldSettings['id']          = [
                'label'   => 'ID',
                'checked' => true,
            ];
            $fieldSettings['title']       = [
                'label'   => 'Title',
                'checked' => true,
            ];
            $fieldSettings['dateCreated'] = [
                'label'   => 'Date Created',
                'checked' => true,
            ];
            $fieldSettings['status']      = [
                'label'   => 'Status',
                'checked' => true,
            ];
        }

        foreach ($form->getLayout()->getFields() as $field) {
            if (
                $field instanceof NoStorageInterface ||
                !$field->getId() ||
                in_array($field->getId(), $storedFieldIds, true)
            ) {
                continue;
            }

            $fieldSettings[$field->getId()] = [
                'label'   => $field->getLabel(),
                'checked' => true,
            ];
        }

        return $fieldSettings;
    }

    /**
     * @return Query
     */
    private function buildCommand()
    {
        $fieldData = $this->getFieldSettings();

        $searchableFields = $labels = [];
        foreach ($fieldData as $fieldId => $data) {
            $isChecked = $data['checked'];

            if (!(bool) $isChecked) {
                continue;
            }

            $fieldName = is_numeric($fieldId) ? SubmissionModel::getFieldColumnName($fieldId) : $fieldId;
            switch ($fieldName) {
                case 'title':
                    $fieldName = 's.' . $fieldName;
                    break;
                case 'status':
                    $fieldName = 'stat.name AS status';
                    break;
                default:
                    $fieldName = 's.' . $fieldName;
                    break;
            }

            $searchableFields[] = $fieldName;
        }

        /** @var Query $command */
        $command = ee()
            ->db
            ->select(implode(',', $searchableFields))
            ->from(SubmissionModel::TABLE . ' s')
            ->join(StatusModel::TABLE . ' stat', 'stat.id = s.statusId')
            ->where('s.formId', $this->formId);

        $dateRangeEnd = $this->getDateRangeEnd();
        if ($dateRangeEnd) {
            $command->where('s.dateCreated >', $dateRangeEnd->format('Y-m-d H:i:s'));
        }

        if ($this->filters) {
            foreach ($this->filters as $filter) {
                $id    = $filter['field'];
                $type  = $filter['type'];
                $value = $filter['value'];

                $fieldId = $id;
                if (is_numeric($id)) {
                    $fieldId = SubmissionModel::getFieldColumnName($id);
                }

                if ($fieldId === 'id') {
                    $fieldId = 's.id';
                }

                if ($fieldId === 'dateCreated') {
                    $fieldId = 's.dateCreated';
                }

                if ($fieldId === 'status') {
                    $fieldId = 'stat.name AS status';
                }

                switch ($type) {
                    case '=':
                        $command->where($fieldId, $value);
                        break;

                    case '!=':
                        $command->where($fieldId . ' !=', $value);
                        break;

                    case 'like':
                        if (preg_match('/^%.+%$/', $value)) {
                            $side  = 'both';
                            $value = substr($value, 1, -1);
                        } else if (preg_match('/^%/', $value)) {
                            $side  = 'left';
                            $value = substr($value, 1);
                        } else if (preg_match('/%$/', $value)) {
                            $side  = 'right';
                            $value = substr($value, 0, -1);
                        } else {
                            $side = 'both';
                        }

                        $command->like($fieldId, $value, $side);
                        break;

                    default:
                        continue 2;
                }
            }
        }

        if ($this->limit) {
            $command->limit((int) $this->limit);
        }

        if (is_array($this->statuses) && !empty($this->statuses)) {
            $command->where_in('statusId', $this->statuses);
        }

        return $command;
    }
}
