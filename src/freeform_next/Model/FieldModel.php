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

/**
 * Class FieldModel
 *
 * @property int    $id
 * @property string $type
 * @property string $handle
 * @property string $label
 * @property bool   $required
 * @property string $groupValueType
 * @property string $value
 * @property bool   $checked
 * @property string $placeholder
 * @property string $instructions
 * @property array  $values
 * @property array  $options
 * @property int    $notificationId
 * @property int    $assetSourceId
 * @property int    $rows
 * @property array  $fileKinds
 * @property int    $maxFileSizeKB
 */
class FieldModel extends Model
{

    protected static $_primary_key = 'id';
    protected static $_table_name  = 'freeform_next_fields';

    protected $id;
    protected $type;
    protected $handle;
    protected $label;
    protected $required;
    protected $groupValueType;
    protected $value;
    protected $checked;
    protected $placeholder;
    protected $instructions;
    protected $values;
    protected $options;
    protected $notificationId;
    protected $assetSourceId;
    protected $rows;
    protected $fileKinds;
    protected $maxFileSizeKB;

    /** @var Composer */
    private $composer;

    /**
     * Creates a Form object with default settings
     *
     * @param int $siteId
     *
     * @return FormModel
     */
    public static function create($siteId)
    {
        /** @var FormModel $form */
        $form = ee('Model')->make(
            'freeform_next:FormModel',
            [
                'site_id' => $siteId,
            ]
        );

        return $form;
    }
}