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
 * @property string $description
 * @property string $fromName
 * @property string $fromEmail
 * @property string $replyToEmail
 * @property bool   $includeAttachments
 * @property string $subject
 * @property string $bodyHtml
 * @property string $bodyText
 * @property int    $sortOrder
 */
class NotificationModel extends Model implements \JsonSerializable
{
    const MODEL = 'freeform_next:NotificationModel';

    protected static $_primary_key = 'id';
    protected static $_table_name  = 'freeform_next_notifications';

    protected $id;
    protected $siteId;
    protected $name;
    protected $handle;
    protected $description;
    protected $fromName;
    protected $fromEmail;
    protected $replyToEmail;
    protected $includeAttachments;
    protected $subject;
    protected $bodyHtml;
    protected $bodyText;
    protected $sortOrder;

    /**
     * Creates a Field object with default settings
     *
     * @return NotificationModel
     */
    public static function create()
    {
        $body = <<<EOT
<p>Submitted on: {{ dateCreated|date('Y-m-d H:i:s') }}</p>
<ul>
{% for field in allFields %}
    <li>{{ field.label }}: {{ field.getValueAsString() }}</li>
{% endfor %}
</ul>
EOT;

        /** @var FieldModel $field */
        $model = ee('Model')->make(
            self::MODEL,
            [
                'siteId'    => ee()->config->item('site_id'),
                'fromName'  => ee()->config->item('webmaster_name'),
                'fromEmail' => ee()->config->item('webmaster_email'),
                'subject'   => 'New submission from {{ form.name }}',
                'bodyHtml'  => $body,
                'bodyText'  => $body,
            ]
        );

        return $model;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id'          => (int) $this->id,
            'name'        => $this->name,
            'handle'      => $this->handle,
            'description' => $this->description,
        ];
    }
}