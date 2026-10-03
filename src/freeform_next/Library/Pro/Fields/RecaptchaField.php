<?php

namespace Solspace\Addons\FreeformNext\Library\Pro\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\InputOnlyInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\SingleValueTrait;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Services\CaptchaWidgetService;

class RecaptchaField extends AbstractField implements NoStorageInterface, SingleValueInterface, InputOnlyInterface
{
    use SingleValueTrait;

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE_RECAPTCHA;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'grecaptcha_' . $this->getHash();
    }

    /**
     * @inheritDoc
     */
    protected function getInputHtml(): bool|string
    {
        $settings = (new SettingsService())->getSettingsModel();
        $widget = (new CaptchaWidgetService())->render($settings);

        return $widget === '' ? false : $widget . '<input type="hidden" name="' . $this->getHandle() . '" />';
    }
}
