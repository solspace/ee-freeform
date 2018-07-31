<?php

namespace Solspace\Addons\FreeformNext\Library\Pro\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\InputOnlyInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\SingleValueTrait;
use Solspace\Addons\FreeformNext\Services\SettingsService;

class RecaptchaField extends AbstractField implements NoStorageInterface, SingleValueInterface, InputOnlyInterface
{
    use SingleValueTrait;

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::TYPE_RECAPTCHA;
    }

    /**
     * @inheritDoc
     */
    public function getHandle()
    {
        return 'grecaptcha_' . $this->getHash();
    }

    /**
     * @inheritDoc
     */
    protected function getInputHtml()
    {
        static $key;

        if ($key === null) {
            $settingsService = new SettingsService();
            $key = $settingsService->getSettingsModel()->getRecaptchaKey();
        }

        $output = '<script src="https://www.google.com/recaptcha/api.js"></script>';
        $output .= '<div class="g-recaptcha" data-sitekey="' . ($key ?: 'invalid') . '"></div>';
        $output .= '<input type="hidden" name="' . $this->getHandle() . '" />';

        return $output;
    }
}
