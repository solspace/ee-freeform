<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\DataObjects\EmailTemplate;

/**
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
 * @property int    $legacyId
 */
class NotificationModel extends Model implements \JsonSerializable
{
    use TimestampableTrait;

    const MODEL = 'freeform_next:NotificationModel';
    const TABLE = 'freeform_next_notifications';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

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
    protected $legacyId;

    /**
     * @return array
     */
    public static function createValidationRules()
    {
        return [
            'name'      => 'required',
            'handle'    => 'required',
            'fromEmail' => 'required',
            'subject'   => 'required',
        ];
    }

    /**
     * Creates a Field object with default settings
     *
     * @return NotificationModel
     */
    public static function create()
    {
        $body = <<<EOT
<p>Submitted on: {date_created format="%l, %F %j, %Y at %g:%i%a"}</p>
<ul>
    {form:fields}
        <li>{field:label}: {field:value}</li>
    {/form:fields}
</ul>
EOT;

        /** @var FieldModel $field */
        $model = ee('Model')->make(
            self::MODEL,
            [
                'siteId'    => ee()->config->item('site_id'),
                'fromName'  => ee()->config->item('site_name'),
                'fromEmail' => ee()->config->item('webmaster_email'),
                'subject'   => 'New submission from your {form:name} form',
                'bodyHtml'  => $body,
                'bodyText'  => $body,
            ]
        );

        return $model;
    }

    /**
     * @param string $filePath
     *
     * @return NotificationModel
     */
    public static function createFromTemplate($filePath)
    {
        $template = new EmailTemplate($filePath);

        $model = ee('Model')->make(
            self::MODEL,
            [
                'id'                 => pathinfo($filePath, PATHINFO_BASENAME),
                'name'               => $template->getName(),
                'handle'             => $template->getHandle(),
                'description'        => $template->getDescription(),
                'fromEmail'          => $template->getFromEmail(),
                'fromName'           => $template->getFromName(),
                'subject'            => $template->getSubject(),
                'replyToEmail'       => $template->getReplyToEmail(),
                'bodyHtml'           => $template->getBody(),
                'bodyText'           => $template->getBody(),
                'includeAttachments' => $template->isIncludeAttachments(),
            ]
        );

        return $model;
    }

    /**
     * @return bool
     */
    public function isFileTemplate()
    {
        return !is_numeric($this->id);
    }

    /**
     * @param int $id
     */
    public function setLegacyId($id)
    {
        $this->set(['legacyId' => $id]);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
	{
        return [
            'id'          => is_numeric($this->id) ? (int) $this->id : $this->id,
            'name'        => $this->name,
            'handle'      => $this->handle,
            'description' => $this->description,
        ];
    }
}
