<?php

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * @property int       $id
 * @property int       $siteId
 * @property int       $mailingListId
 * @property string    $handle
 * @property string    $label
 * @property string    $type
 * @property bool      $required
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 */
class MailingListFieldModel extends Model
{
    use TimestampableTrait;

    const MODEL = 'freeform_next:MailingListFieldModel';
    const TABLE = 'freeform_next_mailing_list_fields';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $mailingListId;
    protected $handle;
    protected $label;
    protected $type;
    protected $required;

    /**
     * @return MailingListFieldModel
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
