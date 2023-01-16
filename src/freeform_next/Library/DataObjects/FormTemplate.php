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

namespace Solspace\Addons\FreeformNext\Library\DataObjects;

use Solspace\Addons\FreeformNext\Library\Helpers\StringHelper;

class FormTemplate implements \JsonSerializable
{
    /** @var string */
    private $filePath;

    /** @var string */
    private $fileName;

    /** @var string */
    private $name;

    /**
     * FormTemplate constructor.
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->fileName = pathinfo($filePath, PATHINFO_BASENAME);
        $this->name     = StringHelper::camelize(StringHelper::humanize(pathinfo($filePath, PATHINFO_FILENAME)));
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
            "name"     => $this->getName(),
            "fileName" => $this->getFileName(),
            "filePath" => $this->getFilePath(),
        ];
    }
}
