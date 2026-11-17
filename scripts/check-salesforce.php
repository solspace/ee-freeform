<?php
/** HTTP regression checks; no Salesforce credentials or EE installation required. */
require __DIR__ . '/../src/freeform_next/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;
use Solspace\Addons\FreeformNext\Integrations\CRM\SalesforceLead;
use Solspace\Addons\FreeformNext\Library\Configuration\ConfigurationInterface;
use Solspace\Addons\FreeformNext\Library\Database\CRMHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\AbstractIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationStorageInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationSecret;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Layout;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\IntegrationProperties;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;

set_error_handler(static function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

/** Encryption-service test double; actual EE encryption is also checked in the local preview. */
function ee($service)
{
    if ($service !== 'Encrypt') { throw new RuntimeException('Unexpected EE service'); }
    static $cipher;
    return $cipher ??= new class {
        public function encrypt($value) { return "\x00\xfftest-cipher:" . strrev($value); }
        public function decrypt($value) {
            return str_starts_with($value, "\x00\xfftest-cipher:") ? strrev(substr($value, strlen("\x00\xfftest-cipher:"))) : false;
        }
    };
}

function check($condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function fails(callable $callback, string $message): void
{
    try {
        $callback();
    } catch (IntegrationException $e) {
        check(strpos($e->getMessage(), $message) !== false, 'Unexpected error: ' . $e->getMessage());
        return;
    }
    throw new RuntimeException('Expected an IntegrationException');
}

class SalesforceHttpFixture extends SalesforceLead
{
    public $stack;
    public array $history = [];
    public $logger;

    protected function createClient(array $config = []): Client
    {
        return new Client(['handler' => $this->stack] + $config);
    }
}

class SalesforceStorageFixture implements IntegrationStorageInterface
{
    public $token;
    public array $settings = [];
    public function updateAccessToken($accessToken) { $this->token = $accessToken; }
    public function updateSettings(array $settings = []) { $this->settings = $settings; }
}

function fixture(array $settings, array $config, array $responses, ?int $id = 1): SalesforceHttpFixture
{
    $logger = new class implements LoggerInterface {
        public array $messages = [];
        public function log($level, $message, $category = self::DEFAULT_LOGGER_CATEGORY) { $this->messages[] = $message; }
        public function debug($message, $category = self::DEFAULT_LOGGER_CATEGORY) { $this->messages[] = $message; }
        public function info($message, $category = self::DEFAULT_LOGGER_CATEGORY) { $this->messages[] = $message; }
        public function warn($message, $category = self::DEFAULT_LOGGER_CATEGORY) { $this->messages[] = $message; }
        public function error($message, $category = self::DEFAULT_LOGGER_CATEGORY) { $this->messages[] = $message; }
        public function fatal($message, $category = self::DEFAULT_LOGGER_CATEGORY) { $this->messages[] = $message; }
    };
    $configuration = new class($config) implements ConfigurationInterface {
        public function __construct(private array $values) {}
        public function get($key) { return $this->values[$key] ?? null; }
    };
    $translator = new class implements TranslatorInterface {
        public function translate($string, array $variables = []) {
            foreach ($variables as $key => $value) { $string = str_replace('{' . $key . '}', $value, $string); }
            return $string;
        }
    };
    $handler = new class implements CRMHandlerInterface {
        public function getAllIntegrations() { return []; }
        public function getIntegrationById($id) { return null; }
        public function updateFields(AbstractCRMIntegration $integration, array $fields) { return true; }
        public function getFields(AbstractCRMIntegration $integration) { return []; }
        public function flagIntegrationForUpdating(AbstractCRMIntegration $integration) {}
        public function pushObject(IntegrationProperties $properties, Layout $layout) { return false; }
        public function onAfterResponse(AbstractIntegration $integration, ResponseInterface $response) {}
    };
    $integration = new SalesforceHttpFixture($id, 'Salesforce test', new DateTime(), 'old-token', $settings, $logger, $configuration, $translator, $handler);
    $integration->logger = $logger;
    $integration->stack = HandlerStack::create(new MockHandler($responses));
    $integration->stack->push(Middleware::history($integration->history));
    return $integration;
}

function token(string $value = 'new-token', string $instance = 'https://example.my.salesforce.com'): Response
{
    return new Response(200, [], json_encode(['access_token' => $value, 'instance_url' => $instance]));
}

function payload(SalesforceHttpFixture $integration, int $index = 0): array
{
    parse_str((string) $integration->history[$index]['request']->getBody(), $data);
    return $data;
}

$legacy = [
    'salesforce_client_id' => 'legacy-id',
    'salesforce_client_secret' => 'legacy-secret+&=',
    'salesforce_username' => 'legacy@example.com',
    'salesforce_password' => 'password+security-token',
];
$credentials = [
    'salesforce_client_credentials_id' => 'eca-id',
    'salesforce_client_credentials_secret' => 'eca-secret+&=',
    'salesforce_my_domain' => 'https://example.my.salesforce.com/',
];
$cc = ['salesforce_client_credentials' => true];

// Old saved records and explicitly disabled controls keep the exact old grant and endpoints.
$newIntegration = fixture([], $credentials, [token()], null);
$newIntegration->fetchAccessToken();
check(payload($newIntegration)['grant_type'] === 'client_credentials', 'New integrations must default to Client Credentials');
$authBlueprint = SalesforceLead::getSettingBlueprints()[0];
check(array_keys($authBlueprint->getChoices()) === ['y', 'n'], 'Client Credentials must be listed first');
check($authBlueprint->getValue(true) === 'y' && $authBlueprint->getValue(false) === 'n', 'Only new integration forms should default to Client Credentials');

foreach ([[], ['salesforce_client_credentials' => false], ['salesforce_client_credentials' => 'n']] as $settings) {
    foreach ([false, true] as $sandbox) {
        $integration = fixture($settings + ['salesforce_sandbox' => $sandbox], $legacy + $credentials, [token()]);
        $storage = new SalesforceStorageFixture();
        $integration->onBeforeSave($storage);
        check($storage->token === 'new-token', 'Legacy save must persist its token');
        check(payload($integration) === ['grant_type' => 'password', 'client_id' => $legacy['salesforce_client_id'], 'client_secret' => $legacy['salesforce_client_secret'], 'username' => $legacy['salesforce_username'], 'password' => $legacy['salesforce_password']], 'Legacy grant must be unchanged');
        $host = $sandbox ? 'test' : 'login';
        check((string) $integration->history[0]['request']->getUri() === "https://{$host}.salesforce.com/services/oauth2/token", 'Legacy endpoint must be unchanged');
    }
}
$integration = fixture([], [], []);
$integration->onBeforeSave(new SalesforceStorageFixture());
check(!$integration->history, 'Incomplete legacy setup must retain its existing save behavior');
fails(fn() => $integration->fetchAccessToken(), 'Consumer Key and Consumer Secret are required');

// A disconnected saved integration reports its state without an invalid or external HTTP request.
foreach ([[], $cc] as $mode) {
    $integration = fixture($mode, [], []);
    check(!$integration->checkConnection(), 'Disconnected integration must report not authorized');
    check(!$integration->history, 'Disconnected integration must not make an HTTP request');
}

// Client Credentials works without legacy settings and remains isolated when both are configured.
foreach ([$credentials, $credentials + $legacy] as $config) {
    foreach ([true, 'y'] as $enabled) {
        $integration = fixture(['salesforce_client_credentials' => $enabled], $config, [token()]);
        $storage = new SalesforceStorageFixture();
        $integration->onBeforeSave($storage);
        check($storage->token === 'new-token' && $storage->settings['instance'] === 'https://example.my.salesforce.com', 'Client Credentials save must persist token and instance');
        check(payload($integration) === ['grant_type' => 'client_credentials', 'client_id' => 'eca-id', 'client_secret' => 'eca-secret+&='], 'Client Credentials must send only the new grant and app credentials');
        check((string) $integration->history[0]['request']->getUri() === 'https://example.my.salesforce.com/services/oauth2/token', 'Client Credentials must use My Domain');
        check($integration->history[0]['options']['allow_redirects'] === false, 'Token requests must not follow redirects');
        check(strpos((string) $integration->history[0]['request']->getUri(), 'secret') === false, 'Credentials must not appear in the URL');
    }
}
$config = array_replace($credentials, ['salesforce_my_domain' => 'https://example--uat.sandbox.my.salesforce.com']);
$integration = fixture($cc + ['salesforce_sandbox' => true], $config, [token()]);
$integration->fetchAccessToken();
check($integration->history[0]['request']->getUri()->getHost() === 'example--uat.sandbox.my.salesforce.com', 'Sandbox must use its own My Domain');

foreach (['', 'http://example.my.salesforce.com', 'https://login.salesforce.com', 'https://test.salesforce.com', 'https://attacker.example', 'https://example.my.salesforce.com.attacker.example', 'https://user:secret@example.my.salesforce.com', 'https://example.my.salesforce.com:443', 'https://example.my.salesforce.com/services/oauth2/token', 'https://example.my.salesforce.com?query=1', 'https://example.my.salesforce.com#fragment'] as $domain) {
    $integration = fixture($cc, array_replace($credentials, ['salesforce_my_domain' => $domain]), []);
    fails(fn() => $integration->onBeforeSave(new SalesforceStorageFixture()), 'salesforce_my_domain');
    check(!$integration->history, 'Invalid domain must fail before HTTP');
}
foreach (['salesforce_client_credentials_id', 'salesforce_client_credentials_secret'] as $key) {
    $config = $credentials;
    unset($config[$key]);
    $integration = fixture($cc, $config + $legacy, []);
    fails(fn() => $integration->onBeforeSave(new SalesforceStorageFixture()), 'Consumer Key and Consumer Secret are required');
    check(!$integration->history, 'Missing Client Credentials must not fall back to legacy credentials');
}

// Per-integration input overrides config.php; stored secrets stay encoded and are decrypted only for auth.
$secret = IntegrationSecret::encrypt('ui-client-secret+&=');
$integration = fixture($cc + [
    'salesforce_my_domain' => 'https://ui-example.my.salesforce.com',
    'salesforce_client_credentials_id' => 'ui-client-id',
    'salesforce_client_credentials_secret' => $secret,
], $credentials + $legacy, [token()]);
$integration->fetchAccessToken();
check(payload($integration)['client_id'] === 'ui-client-id' && payload($integration)['client_secret'] === 'ui-client-secret+&=', 'Saved UI credentials must override config values');
check($integration->history[0]['request']->getUri()->getHost() === 'ui-example.my.salesforce.com', 'Saved My Domain must override the shared config');
check($integration->getSettings()['salesforce_client_credentials_secret'] === $secret, 'Reading a credential must not replace encrypted storage with plaintext');
check(strpos(json_encode($integration->getSettings()), 'ui-client-secret') === false, 'Stored settings must not contain plaintext secrets');
check(strpos(json_encode($integration), 'client_secret') === false, 'Integration JSON must not expose credential settings');
$integration = fixture([
    'salesforce_client_id' => 'ui-legacy-id',
    'salesforce_client_secret' => IntegrationSecret::encrypt('ui-legacy-secret'),
    'salesforce_username' => 'ui@example.com',
    'salesforce_password' => IntegrationSecret::encrypt('ui-password'),
    'salesforce_security_token' => IntegrationSecret::encrypt('ui-token'),
], $legacy, [token()]);
$integration->fetchAccessToken();
check(payload($integration)['username'] === 'ui@example.com' && payload($integration)['password'] === 'ui-passwordui-token', 'Legacy inputs must support a separately masked security token');
check(payload($integration)['client_id'] === 'ui-legacy-id' && payload($integration)['client_secret'] === 'ui-legacy-secret', 'Legacy UI credentials must override config');
$integration = fixture($cc + ['salesforce_client_credentials_secret' => 'invalid-ciphertext'], $credentials, []);
fails(fn() => $integration->fetchAccessToken(), 'Unable to decrypt');
check(!$integration->history, 'Unreadable saved secrets must fail before HTTP');
foreach (SalesforceLead::getSettingBlueprints() as $blueprint) {
    if (in_array($blueprint->getHandle(), ['salesforce_client_credentials_secret', 'salesforce_client_secret', 'salesforce_password', 'salesforce_security_token'], true)) {
        check($blueprint->isEditable() && $blueprint->isSecret() && $blueprint->getInputType() === 'password', 'Credential secrets must use editable password fields');
    }
}

// Reissue access tokens using the selected flow; failed auth cannot report the old token as refreshed.
foreach ([[], $cc] as $mode) {
    $integration = fixture($mode, $credentials + $legacy, [token(), new Response(400, [], '{"error":"invalid_client","error_description":"eca-secret+&="}')]);
    check($integration->refreshToken(), 'Initial refresh should succeed');
    check(!$integration->refreshToken() && !$integration->isAccessTokenUpdated(), 'Failed refresh must report failure');
    check(count($integration->history) === 2 && !isset(payload($integration, 1)['refresh_token']), 'Renewal must use the grant rather than a refresh token');
    check(strpos(implode('', $integration->logger->messages), 'eca-secret') === false, 'Auth logs must not expose secrets');
}
foreach ([new Response(200, [], '{}'), new Response(200, [], 'invalid JSON'), new Response(200, [], '{"access_token":"","instance_url":"https://example.my.salesforce.com"}'), new Response(200, [], '{"access_token":"new-token"}'), new Response(200, [], '{"access_token":"new-token","instance_url":""}'), new Response(302, ['Location' => 'https://attacker.example']), new ConnectException('eca-secret', new Request('POST', 'https://example.my.salesforce.com'))] as $response) {
    $integration = fixture($cc, $credentials, [$response]);
    $storage = new SalesforceStorageFixture();
    fails(fn() => $integration->onBeforeSave($storage), '');
    check($storage->token === null && !$integration->isAccessTokenUpdated() && $integration->getAccessToken() === 'old-token', 'Bad token response must not replace or persist an old token');
}

// Field discovery renews on 401 and sends the fresh bearer token to the new instance.
foreach ([[], $cc] as $mode) {
    $integration = fixture($mode + ['instance' => 'https://old.my.salesforce.com'], $credentials + $legacy, [new Response(401), token(), new Response(200, [], '{"fields":[{"name":"Email","label":"Email","type":"email","updateable":true,"referenceTo":[],"nillable":false}]}')]);
    $fields = $integration->fetchFields();
    check(count($fields) === 1 && $fields[0]->getHandle() === 'Email', 'Field mapping must survive expired access tokens');
    check($integration->history[2]['request']->getHeaderLine('Authorization') === 'Bearer new-token', 'Field discovery must use the renewed bearer token');
    check($integration->history[2]['request']->getUri()->getHost() === 'example.my.salesforce.com', 'Field discovery must use the updated instance');
}

// Lead payload, assignment rules, and the existing refresh-and-retry contract are preserved.
foreach ([[], $cc] as $mode) {
    $integration = fixture($mode + ['instance' => 'https://example.my.salesforce.com', 'salesforce_lead_owner' => true], $credentials + $legacy, [new Response(401), token(), new Response(201)]);
    try {
        $integration->pushObject(['LastName' => 'Test', 'Company' => 'Example', 'Email' => 'test@example.com']);
        throw new RuntimeException('Expected expired token');
    } catch (ClientException $e) {
        check($e->getResponse()->getStatusCode() === 401, 'Service must receive the auth error for its retry');
    }
    check($integration->refreshToken(), 'Lead retry must obtain a fresh token');
    check($integration->pushObject(['LastName' => 'Test', 'Company' => 'Example', 'Email' => 'test@example.com']), 'Lead creation must succeed with fresh credentials');
    $request = $integration->history[2]['request'];
    check($request->getHeaderLine('Authorization') === 'Bearer new-token', 'Lead must use refreshed token');
    check($request->getHeaderLine('Sforce-Auto-Assign') === 'TRUE', 'Lead assignment behavior must be preserved');
    check($request->getUri()->getPath() === '/services/data/v44.0/sobjects/Lead', 'Lead endpoint must be preserved');
    check(json_decode((string) $request->getBody(), true)['Email'] === 'test@example.com', 'Mapped Lead fields must be preserved');
}

echo "Salesforce authentication, legacy compatibility, renewal, field mapping, and Lead creation checks passed.\n";
