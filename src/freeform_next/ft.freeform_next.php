<?php

use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class Freeform_next_ft extends EE_Fieldtype
{
    /** @var array */
    public $info = [
        'name'    => 'Freeform Next',
        'version' => '1.0',
    ];

    /**
     * Freeform_next_ft constructor.
     */
    public function __construct()
    {
        parent::__construct();

        ee()->lang->loadfile('freeform_next');

        if (REQ !== 'CP' && !session_id()) {
            @session_start();
        }

        $this->info = include __DIR__ . '/addon.setup.php';

        $this->field_id = isset($this->settings['field_id']) ?
            $this->settings['field_id'] :
            $this->field_id;

        $this->field_name = isset($this->settings['field_name']) ?
            $this->settings['field_name'] :
            $this->field_name;
    }

    /**
     * @inheritdoc
     */
    public function update($version = '')
    {
        return $version && version_compare($this->info['version'], $version, '>');
    }

    /**
     * @inheritdoc
     */
    public function display_field($data)
    {
        $formRepository = FormRepository::getInstance();

        $opts  = [
            0 => '--',
        ];
        $forms = $formRepository->getAllForms();
        foreach ($forms as $form) {
            $opts[$form->id] = $form->name;
        }

        if (empty($forms)) {
            return '<p style="margin-top:0;margin-bottom:0;">' .
                lang('no_available_composer_forms', $this->field_name) .
                '</p>';
        }

        return form_dropdown($this->field_name, $opts, $data);
    }

    /**
     * @inheritdoc
     */
    public function replace_tag($data, $params = [], $tagdata = false)
    {
        $formId    = (int) $data;
        $formModel = FormRepository::getInstance()->getFormById($formId);

        if (!$formModel) {
            return '';
        }

        $hash = ee()->input->post(FormValueContext::FORM_HASH_KEY, null);
        if (null !== $hash) {
            if (!class_exists('Freeform_Next')) {
                require_once __DIR__ . '/mod.freeform_next.php';
            }

            $obj = new Freeform_Next();
            $obj->submitForm();
        }

        $form = $formModel->getForm();

        return $form->render();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function accepts_content_type($name)
    {
        return in_array($name, ['channel', 'fluid_field', 'grid'], true);
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function save($data)
    {
        if ((int) $data === 0) {
            return parent::save(null);
        }

        return parent::save($data);
    }
}
