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
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;

/**
 * Class FieldModel
 *
 * @property int    $id
 * @property int    $siteId
 * @property int    $statusId
 * @property int    $formId
 * @property string $title
 */
class SubmissionModel extends Model
{
    const MODEL = 'freeform_next:SubmissionModel';
    const TABLE = 'freeform_next_submissions';

    const FIELD_COLUMN_PREFIX = 'field_';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $statusId;
    protected $formId;
    protected $title;

    /**
     * Get the submission table field column name
     *
     * @param int $fieldId
     *
     * @return string
     */
    public static function getFieldColumnName($fieldId)
    {
        return self::FIELD_COLUMN_PREFIX . $fieldId;
    }

    /**
     * Creates a Field object with default settings
     *
     * @return SubmissionModel
     */
    public static function create()
    {
        /** @var SubmissionModel $field */
        $field = ee('Model')->make(
            self::MODEL,
            [
                'siteId' => ee()->config->item('site_id'),
            ]
        );

        return $field;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return HashHelper::hash($this->id);
    }
}
