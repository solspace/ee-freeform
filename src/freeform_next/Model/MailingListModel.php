<?php

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;

/**
 * @property int       $id
 * @property int       $siteId
 * @property int       $integrationId
 * @property int       $resourceId
 * @property string    $name
 * @property int       $memberCount
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 */
class MailingListModel extends Model
{
    use TimestampableTrait;

    const MODEL = 'freeform_next:MailingListModel';
    const TABLE = 'freeform_next_mailing_lists';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $integrationId;
    protected $resourceId;
    protected $name;
    protected $memberCount;

    /**
     * @return MailingListModel
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

    /**
     * @return FieldObject[]
     */
    public function getFieldObjects()
    {
        /** @var MailingListFieldModel[] $fields */
        $fields = ee('Model')
            ->get(MailingListFieldModel::MODEL)
            ->filter('mailingListId', $this->id)
            ->all();

        $fieldObjects = [];
        foreach ($fields as $field) {
            $fieldObjects[] = new FieldObject(
                $field->handle,
                $field->label,
                $field->type,
                $field->required
            );
        }

        return $fieldObjects;
    }
}
