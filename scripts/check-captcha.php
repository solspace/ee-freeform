<?php

namespace EllisLab\ExpressionEngine\Service\Model {
    class Model {}
}

namespace {
    use GuzzleHttp\Client;
    use GuzzleHttp\Handler\MockHandler;
    use GuzzleHttp\HandlerStack;
    use GuzzleHttp\Middleware;
    use GuzzleHttp\Psr7\Response;
    use Solspace\Addons\FreeformNext\Library\Pro\Fields\RecaptchaField;
    use Solspace\Addons\FreeformNext\Model\SettingsModel;
    use Solspace\Addons\FreeformNext\Services\CaptchaWidgetService;

    require dirname(__DIR__) . '/src/freeform_next/vendor/autoload.php';

    function lang(string $text): string { return $text; }
    function check(bool $result, string $message): void
    {
        if (!$result) {
            throw new \RuntimeException($message);
        }
        echo "PASS: $message\n";
    }
    function settings(array $values): SettingsModel
    {
        $model = (new \ReflectionClass(SettingsModel::class))->newInstanceWithoutConstructor();
        foreach ($values as $key => $value) {
            (new \ReflectionProperty(SettingsModel::class, $key))->setValue($model, $value);
        }
        return $model;
    }

    $legacy = settings(['recaptchaEnabled' => true, 'recaptchaKey' => 'legacy-key', 'recaptchaSecret' => 'legacy-secret']);
    check($legacy->getCaptchaProvider() === 'recaptcha' && $legacy->isRecaptchaEnabled(), 'existing reCAPTCHA switch remains active after upgrade');
    $none = settings(['recaptchaEnabled' => true, 'captchaProvider' => 'none']);
    check($none->getCaptchaProvider() === 'none' && !$none->isRecaptchaEnabled(), 'explicit None overrides the legacy switch');

    $turnstile = settings(['captchaProvider' => 'turnstile', 'turnstileKey' => 'public"><script>', 'turnstileSecret' => 'private']);
    $widget = (new CaptchaWidgetService())->render($turnstile);
    check(str_contains($widget, 'api.js?render=explicit') && !str_contains($widget, 'public"><script>') && str_contains($widget, 'public&quot;&gt;&lt;script&gt;'), 'Turnstile widget renders and escapes its site key');
    $hcaptcha = settings(['captchaProvider' => 'hcaptcha', 'hcaptchaKey' => 'site-key', 'hcaptchaSecret' => 'private']);
    check(str_contains((new CaptchaWidgetService())->render($hcaptcha), 'js.hcaptcha.com') && str_contains((new CaptchaWidgetService())->render($hcaptcha), 'api.js?render=explicit'), 'hCaptcha widget renders');
    check((new CaptchaWidgetService())->render($none) === '', 'None renders no challenge');

    $history = [];
    $mock = new MockHandler([
        new Response(200, [], '{"success":true}'),
        new Response(200, [], '{"success":true}'),
        new Response(200, [], '{"success":true}'),
        new Response(200, [], '{"success":false}'),
        new Response(200, [], 'not-json'),
        new \RuntimeException('network unavailable'),
    ]);
    $handler = HandlerStack::create($mock);
    $handler->push(Middleware::history($history));
    $service = new CaptchaWidgetService(new Client(['handler' => $handler]));
    foreach (['turnstile', 'hcaptcha', 'recaptcha'] as $provider) {
        check($service->verify($provider, 'token', 'private', 'site-key'), "$provider accepts a verified token");
    }
    check(str_contains((string) $history[0]['request']->getUri(), 'challenges.cloudflare.com/turnstile/v0/siteverify'), 'Turnstile uses Cloudflare Siteverify');
    check(str_contains((string) $history[1]['request']->getUri(), 'api.hcaptcha.com/siteverify') && str_contains((string) $history[1]['request']->getBody(), 'sitekey=site-key'), 'hCaptcha sends a form-encoded expected site key');
    check(!$service->verify('turnstile', 'invalid', 'private', 'site-key'), 'failed verification blocks submission');
    check(!$service->verify('hcaptcha', 'invalid', 'private', 'site-key'), 'malformed verification response blocks submission');
    check(!$service->verify('turnstile', 'invalid', 'private', 'site-key'), 'network error blocks submission');
    check(!$service->verify('none', 'token', 'private', 'site-key') && count($history) === 6, 'unknown provider makes no network request');

    $field = (new \ReflectionClass(RecaptchaField::class))->newInstanceWithoutConstructor();
    $service->validateField($field, $turnstile, '');
    check($field->hasErrors(), 'missing token rejects the CAPTCHA field');
}
