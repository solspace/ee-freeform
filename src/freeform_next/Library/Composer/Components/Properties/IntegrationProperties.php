<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Properties;

class IntegrationProperties extends AbstractProperties
{
    /** @var int */
    protected $integrationId;

    /** @var array */
    protected $mapping;

    /**
     * @return int
     */
    public function getIntegrationId()
    {
        return (int)$this->integrationId ?: null;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return !empty($this->mapping) ? $this->mapping : null;
    }

    /**
     * Return a list of all property fields and their type
     *
     * [propertyKey => propertyType, ..]
     * E.g. ["name" => "string", ..]
     *
     * @return array
     */
    protected function getPropertyManifest()
    {
        return [
            'integrationId' => self::TYPE_INTEGER,
            'mapping'       => self::TYPE_ARRAY,
        ];
    }
}
