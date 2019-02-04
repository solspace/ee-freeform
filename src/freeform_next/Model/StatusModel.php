<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;

/**
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

    protected static $_events = ['beforeInsert', 'beforeUpdate'];

    protected $id;
    protected $siteId;
    protected $name;
    protected $handle;
    protected $isDefault;
    protected $color;
    protected $sortOrder;

    /**
     * @return array
     */
    public static function createValidationRules()
    {
        return [
            'name'      => 'required',
            'handle'    => 'required',
        ];
    }

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

    /**
     * Event beforeInsert sets the $dateCreated and $dateUpdated properties
     */
    public function onBeforeInsert()
    {
        $this->set(
            [
                'dateCreated' => $this->getTimestampableDate(),
                'dateUpdated' => $this->getTimestampableDate(),
            ]
        );
    }

    /**
     * Event beforeUpdate sets the $dateUpdated property
     */
    public function onBeforeUpdate()
    {
        $this->set(['dateUpdated' => $this->getTimestampableDate()]);
    }

    /**
     * @return \DateTime
     */
    private function getTimestampableDate()
    {
        return date('Y-m-d H:i:s');
    }
}
