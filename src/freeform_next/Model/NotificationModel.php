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

class NotificationModel extends Model
{
    const MODEL = 'freeform_next:NotificationModel';

    protected static $_primary_key = 'id';
    protected static $_table_name  = 'freeform_next_notifications';

    protected $id;
    protected $siteId;
    protected $type;
    protected $handle;
    protected $label;
    protected $required;
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

    /**
     * Creates a Field object with default settings
     *
     * @return FieldModel
     */
    public static function create()
    {
        /** @var FieldModel $field */
        $field = ee('Model')->make(
            self::MODEL,
            [
                'siteId'   => ee()->config->item('site_id'),
                'required' => false,
            ]
        );

        return $field;
    }
}