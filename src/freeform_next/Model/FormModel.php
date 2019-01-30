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
use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Composer;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\FieldsService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Services\StatusesService;
use Solspace\Addons\FreeformNext\Services\SubmissionsService;

/**
 * @property int    $id
 * @property int    $siteId
 * @property string $name
 * @property string $handle
 * @property int    $spamBlockCount
 * @property string $description
 * @property string $layoutJson
 * @property string $returnUrl
 * @property string $defaultStatus
 * @property int    $legacyId
 */
class FormModel extends Model
{
    const MODEL = 'freeform_next:FormModel';
    const TABLE = 'freeform_next_forms';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected static $_events = ['beforeInsert', 'beforeUpdate', 'beforeSave', 'beforeDelete'];

    protected $id;
    protected $siteId;
    protected $name;
    protected $handle;
    protected $spamBlockCount;
    protected $description;
    protected $layoutJson;
    protected $returnUrl;
    protected $defaultStatus;
    protected $legacyId;
    protected $dateCreated;
    protected $dateUpdated;

    /** @var Composer */
    private $composer;

    /**
     * Creates a Form object with default settings
     *
     * @return FormModel
     */
    public static function create()
    {
        $defaultStatusId = StatusRepository::getInstance()->getDefaultStatusId();

        /** @var FormModel $form */
        $form = ee('Model')->make(
            self::MODEL,
            [
                'siteId'        => ee()->config->item('site_id'),
                'defaultStatus' => $defaultStatusId,
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

    /**
     * Sets names, handles, descriptions
     * And updates the layout JSON
     *
     * @param Composer $composer
     */
    public function setLayout(Composer $composer)
    {
        $form = $composer->getForm();
        $this->set(
            [
                'name'          => $form->getName(),
                'handle'        => $form->getHandle(),
                'description'   => $form->getDescription(),
                'defaultStatus' => $form->getDefaultStatus(),
                'returnUrl'     => $form->getReturnUrl(),
                'layoutJson'    => $composer->getComposerStateJSON(),
            ]
        );
    }

    /**
     * Assembles the composer object and returns it
     *
     * @return Composer
     */
    public function getComposer()
    {
        if (null === $this->composer) {
            $composerState  = json_decode($this->layoutJson, true);
            $formAttributes = $this->getFormAttributes();

            $this->composer = new Composer(
                $composerState,
                $formAttributes,
                new FormsService(),
                new FieldsService(),
                new SubmissionsService(),
                new MailerService(),
                new FilesService(),
                new MailingListsService(),
                new CrmService(),
                new StatusesService(),
                new EETranslator()
            );
        }

        return $this->composer;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->getComposer()->getForm();
    }

    /**
     * @param int $id
     */
    public function setLegacyId($id)
    {
        $this->set(['legacyId' => $id]);
    }

    /**
     * @return FormAttributes
     */
    private function getFormAttributes()
    {
        $sessionImplementation = (new SettingsService())->getSessionStorageImplementation();

        $attributes = new FormAttributes($this->id, $sessionImplementation, new EERequest());
        $attributes
            ->setActionUrl(null)
            ->setCsrfEnabled(false)
            ->setCsrfToken(null)
            ->setCsrfTokenName(null);

        return $attributes;
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

    /**
     * Event beforeSave validates the form
     */
    public function onBeforeSave()
    {
        FreeformHelper::get('validate', $this);
    }

    /**
     * Event beforeSave validates the form
     */
    public function onBeforeDelete()
    {
        FreeformHelper::get('validate', $this);
    }
}
