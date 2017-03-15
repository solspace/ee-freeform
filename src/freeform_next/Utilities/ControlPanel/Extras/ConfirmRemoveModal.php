<?php

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras;

class ConfirmRemoveModal extends Modal
{
    /** @var string */
    private $url;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $kind;

    /** @var string */
    private $plural;

    /** @var array */
    private $variables;

    /**
     * ConfirmRemoveModal constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url       = $url;
        $this->variables = [];
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param string $kind
     *
     * @return $this
     */
    public function setKind($kind)
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * @param string $plural
     *
     * @return $this
     */
    public function setPlural($plural)
    {
        $this->plural = $plural;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addVariable($key, $value)
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * Combines all variables and adds the Modal to the CP
     */
    public function compile()
    {
        $variables = [
            'name'      => 'modal-confirm-remove',
            'form_url'  => $this->url,
            'plural'    => $this->plural,
            'checklist' => [
                [
                    'kind' => $this->kind,
                    'desc' => '',
                ],
            ],
            'desc'      => $this->description ?: '',
        ];

        ee('CP/Modal')->addModal('remove', ee('View')->make('_shared/modal_confirm_remove')->render($variables));
        ee()->javascript->set_global('lang.remove_confirm', $this->kind . ': <b>### ' . $this->plural . '</b>');

        ee()->cp->add_js_script(['file' => ['cp/confirm_remove']]);
    }
}
