<?php

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * @property int       $id
 * @property int       $siteId
 * @property int       $integrationId
 * @property string    $handle
 * @property string    $label
 * @property string    $type
 * @property bool      $required
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 */
class CrmFieldModel extends Model
{
    const MODEL = 'freeform_next:CrmFieldModel';
    const TABLE = 'freeform_next_crm_fields';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $integrationId;
    protected $handle;
    protected $label;
    protected $type;
    protected $required;
    protected $dateCreated;
    protected $dateUpdated;

    /**
     * @return CrmFieldModel
     */
    public static function create()
    {
        return ee('Model')
            ->make(
                self::MODEL,
                [
                    'siteId' => ee()->config->item('site_id'),
                ]
            );
    }
}
