<?php

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * @property int    $id
 * @property int    $site_id
 * @property string $name
 * @property string $handle
 * @property int $spamBlockCount
 * @property string $description
 * @property string $layoutJson
 * @property string $returnUrl
 * @property string $defaultStatus
 * @property string $dateCreated
 * @property string $dateUpdated
 */
class FormModel extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name  = 'freeform_next_forms';

    protected $id;
    protected $site_id;
    protected $name;
    protected $handle;
    protected $spamBlockCount;
    protected $description;
    protected $layoutJson;
    protected $returnUrl;
    protected $defaultStatus;
    protected $dateCreated;
    protected $dateUpdated;

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

    /**
     * Returns the name of this calendar if toString() is invoked
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
