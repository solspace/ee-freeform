<?php

namespace Solspace\Addons\FreeformNext\Library\Database;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Configuration\ExternalOptionsConfiguration;

interface FieldHandlerInterface
{
    /**
     * Perform actions with a field before validation takes place
     *
     * @param AbstractField $field
     * @param Form          $form
     */
    public function beforeValidate(AbstractField $field, Form $form);

    /**
     * Perform actions with a field after validation takes place
     *
     * @param AbstractField $field
     * @param Form          $form
     */
    public function afterValidate(AbstractField $field, Form $form);

    /**
     * @param string $source
     * @param mixed  $target
     * @param array  $configuration
     * @param mixed  $selectedValues
     *
     * @return Option[]
     */
    public function getOptionsFromSource($source, $target, array $configuration = [], $selectedValues = []);
}
