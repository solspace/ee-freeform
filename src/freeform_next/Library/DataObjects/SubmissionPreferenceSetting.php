<?php

namespace Solspace\Addons\FreeformNext\Library\DataObjects;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;

class SubmissionPreferenceSetting implements \JsonSerializable
{
    /** @var int */
    private $id;

    /** @var string */
    private $handle;

    /** @var string */
    private $label;

    /** @var bool */
    private $checked;

    /**
     * @param AbstractField $field
     * @param bool          $checked
     *
     * @return SubmissionPreferenceSetting
     */
    public static function createFromField(AbstractField $field, $checked)
    {
        return new SubmissionPreferenceSetting(
            $field->getId(),
            $field->getHandle(),
            $field->getLabel(),
            $checked
        );
    }

    /**
     * @param array $data
     *
     * @return SubmissionPreferenceSetting
     */
    public static function createFromArray(array $data)
    {
        return new SubmissionPreferenceSetting(
            $data['id'],
            $data['handle'],
            $data['label'],
            $data['checked']
        );
    }

    /**
     * SubmissionPreferenceSetting constructor.
     *
     * @param int    $id
     * @param string $handle
     * @param string $label
     * @param bool   $checked
     */
    public function __construct($id, $handle, $label, $checked)
    {
        $this->id      = $id;
        $this->handle  = $handle;
        $this->label   = $label;
        $this->checked = (bool) $checked;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return is_numeric($this->id) ? (int) $this->id : $this->id;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->checked;
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize(): array
    {
        return [
            'id'      => $this->getId(),
            'handle'  => $this->getHandle(),
            'label'   => $this->getLabel(),
            'checked' => $this->isChecked(),
        ];
    }
}
