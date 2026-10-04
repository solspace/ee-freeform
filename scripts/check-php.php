<?php
error_reporting(E_ALL);
set_error_handler(static function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});
require dirname(__DIR__) . '/src/freeform_next/vendor/autoload.php';
function check(bool $condition, string $label): void {
    if (!$condition) {
        throw new RuntimeException($label);
    }
    echo "PASS: $label\n";
}
$hashids = new Hashids\Hashids('freeform-check');
check($hashids->decode($hashids->encode(42)) === [42], 'Hashids round trip');
$validator = new Egulias\EmailValidator\EmailValidator();
check($validator->isValid('customer@example.com', new Egulias\EmailValidator\Validation\RFCValidation()), 'valid email accepted');
check(!$validator->isValid('invalid address', new Egulias\EmailValidator\Validation\RFCValidation()), 'invalid email rejected');
check((string) Stringy\Stringy::create('First Name')->underscored() === 'first_name', 'field handle conversion');
$accessor = Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();
$data = ['field'=>['value'=>'hello']];
check($accessor->getValue($data, '[field][value]') === 'hello', 'property access');
$directory = sys_get_temp_dir() . '/freeform-v4-php-' . getmypid();
$filesystem = new Symfony\Component\Filesystem\Filesystem();
$filesystem->mkdir($directory);
$filesystem->dumpFile($directory . '/test.txt', 'hello');
$finder = new Symfony\Component\Finder\Finder();
check($finder->files()->in($directory)->count() === 1, 'filesystem and finder');
$filesystem->remove($directory);
$uri = new GuzzleHttp\Psr7\Uri('https://example.com/path');
check($uri->getHost() === 'example.com', 'HTTP URI parsing');
$mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response(200, [], '{"ok":true}')]);
$client = new GuzzleHttp\Client(['handler'=>GuzzleHttp\HandlerStack::create($mock)]);
check((string)$client->get('https://example.com')->getBody() === '{"ok":true}', 'integration HTTP client');
$transformer = new ReflectionMethod(Solspace\Addons\FreeformNext\Library\EETags\Transformers\SubmissionTransformer::class, 'transformSubmission');
check($transformer->getParameters()[4]->getType()->allowsNull(), 'submission transformer nullable parameter');
echo 'Runtime: ' . PHP_VERSION . "\n";
