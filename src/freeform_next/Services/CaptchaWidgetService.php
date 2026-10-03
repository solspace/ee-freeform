<?php

namespace Solspace\Addons\FreeformNext\Services;

use GuzzleHttp\Client;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\RecaptchaField;
use Solspace\Addons\FreeformNext\Model\SettingsModel;
use Throwable;

/** Visible CAPTCHA widgets share the saved legacy reCAPTCHA field type. */
class CaptchaWidgetService
{
    public function __construct(private ?Client $client = null)
    {
    }

    public function render(SettingsModel $settings): string
    {
        $provider = $settings->getCaptchaProvider();
        if ($provider === SettingsModel::CAPTCHA_NONE
            || ($provider === SettingsModel::CAPTCHA_RECAPTCHA && $settings->getRecaptchaType() === 'v3')) {
            return '';
        }

        $key = (string) $settings->getCaptchaSiteKey();
        if ($key === '' || !$settings->getCaptchaSecret()) {
            return '';
        }

        $safeKey = htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        if ($provider === SettingsModel::CAPTCHA_RECAPTCHA) {
            return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>'
                . '<div class="g-recaptcha" data-sitekey="' . $safeKey . '"></div>';
        }

        if (!in_array($provider, [SettingsModel::CAPTCHA_TURNSTILE, SettingsModel::CAPTCHA_HCAPTCHA], true)) {
            return '';
        }

        $script = $provider === SettingsModel::CAPTCHA_TURNSTILE
            ? 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit'
            : 'https://js.hcaptcha.com/1/api.js?render=explicit';
        $safeProvider = htmlspecialchars($provider, ENT_QUOTES, 'UTF-8');

        return '<div class="freeform-captcha" data-freeform-captcha="' . $safeProvider
            . '" data-sitekey="' . $safeKey . '"></div>'
            . '<script>(function(){'
            . 'var provider=' . json_encode($provider) . ',url=' . json_encode($script) . ';'
            . 'var state=window.freeformCaptchaWidgets=window.freeformCaptchaWidgets||{};'
            . 'if(state[provider]){state[provider].render();return;}'
            . 'function render(){var api=window[provider];if(!api||!api.render)return;'
            . 'document.querySelectorAll("[data-freeform-captcha=\""+provider+"\"]").forEach(function(el){'
            . 'if(el.dataset.freeformWidgetId)return;'
            . 'el.dataset.freeformWidgetId=String(api.render(el,{sitekey:el.dataset.sitekey}));});}'
            . 'state[provider]={render:render};'
            . 'var loader=document.createElement("script");loader.src=url;loader.async=true;'
            . 'loader.onload=render;document.head.appendChild(loader);'
            . 'new MutationObserver(render).observe(document.documentElement,{childList:true,subtree:true});'
            . 'document.addEventListener("freeform:captcha-reset",function(event){'
            . 'if(!event.detail||!event.detail.form)return;'
            . 'event.detail.form.querySelectorAll("[data-freeform-captcha=\""+provider+"\"]").forEach(function(el){'
            . 'if(window[provider]&&el.dataset.freeformWidgetId)window[provider].reset(Number(el.dataset.freeformWidgetId));});'
            . '});'
            . '})();</script>';
    }

    public function validateField(RecaptchaField $field, SettingsModel $settings, mixed $token): void
    {
        $provider = $settings->getCaptchaProvider();
        if ($provider === SettingsModel::CAPTCHA_NONE
            || ($provider === SettingsModel::CAPTCHA_RECAPTCHA && $settings->getRecaptchaType() === 'v3')) {
            return;
        }

        if (!is_string($token) || $token === '' || !$settings->getCaptchaSiteKey()
            || !$settings->getCaptchaSecret()
            || !$this->verify($provider, $token, $settings->getCaptchaSecret(), $settings->getCaptchaSiteKey())) {
            $field->addError(lang('Please verify that you are not a robot.'));
        }
    }

    public function verify(string $provider, string $token, string $secret, string $siteKey): bool
    {
        $url = match ($provider) {
            SettingsModel::CAPTCHA_RECAPTCHA => 'https://www.google.com/recaptcha/api/siteverify',
            SettingsModel::CAPTCHA_TURNSTILE => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            SettingsModel::CAPTCHA_HCAPTCHA => 'https://api.hcaptcha.com/siteverify',
            default => null,
        };

        if (!$url || $token === '' || $secret === '') {
            return false;
        }

        $params = ['secret' => $secret, 'response' => $token];
        if ($provider === SettingsModel::CAPTCHA_HCAPTCHA) {
            $params['sitekey'] = $siteKey;
        }

        try {
            $response = ($this->client ?? new Client())->post($url, [
                'form_params' => $params,
                'timeout' => 4.0,
            ]);
            $data = json_decode((string) $response->getBody(), true);

            return is_array($data) && ($data['success'] ?? false) === true;
        } catch (Throwable) {
            return false;
        }
    }
}
