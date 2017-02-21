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
 * Class NotificationModel
 *
 * @property int    $id
 * @property int    $siteId
 * @property string $name
 * @property string $handle
 * @property bool   $isDefault
 * @property string $color
 * @property int    $sortOrder
 */
class StatusModel extends Model implements \JsonSerializable
{
    const MODEL = 'freeform_next:StatusModel';
    const TABLE = 'freeform_next_statuses';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $name;
    protected $handle;
    protected $isDefault;
    protected $color;
    protected $sortOrder;

    /**
     * Creates a Status object with default settings
     *
     * @return StatusModel
     */
    public static function create()
    {
        /** @var StatusModel $field */
        $model = ee('Model')->make(
            self::MODEL,
            [
                'siteId'    => ee()->config->item('site_id'),
            ]
        );

        return $model;
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize()
    {
        return [
            'id'        => (int)$this->id,
            'name'      => $this->name,
            'handle'    => $this->handle,
            'isDefault' => (bool)$this->isDefault,
            'color'     => $this->color,
        ];
    }
}
