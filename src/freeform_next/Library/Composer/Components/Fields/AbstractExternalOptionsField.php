<?php

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ExternalOptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\OptionsKeyValuePairTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\OptionsTrait;

abstract class AbstractExternalOptionsField extends AbstractField implements ExternalOptionsInterface
{
    use OptionsKeyValuePairTrait;
    use OptionsTrait;

    /** @var string */
    protected $source;

    /** @var int|string */
    protected $target;

    /** @var array */
    protected $configuration;

    /**
     * @inheritDoc
     */
    public function getOptionSource()
    {
        return $this->source ?: self::SOURCE_CUSTOM;
    }

    /**
     * @inheritDoc
     */
    public function getOptionTarget()
    {
        return $this->target;
    }

    /**
     * @inheritDoc
     */
    public function getOptionConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return Option[]
     */
    public function getOptions()
    {
        if ($this->getOptionSource() === self::SOURCE_CUSTOM) {
            return $this->options;
        }

        return $this
            ->getForm()
            ->getFieldHandler()
            ->getOptionsFromSource(
                $this->getOptionSource(),
                $this->getOptionTarget(),
                $this->getOptionConfiguration(),
                $this->getValue()
            );
    }
}
