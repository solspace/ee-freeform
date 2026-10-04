<?php

// Run with `php scripts/check-admin-recipients.php`; no EE installation required.
require dirname(__DIR__) . '/src/freeform_next/vendor/autoload.php';

use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\AdminNotificationProperties;

$properties = (new ReflectionClass(AdminNotificationProperties::class))->newInstanceWithoutConstructor();
$reflection = new ReflectionProperty(AdminNotificationProperties::class, 'recipients');
$reflection->setValue($properties, " first+tag@example.com \r\ninvalid@@example.com\nsecond@example.org\rfirst+tag@example.com\nname with spaces@example.com\n");

$actual = array_values($properties->getRecipientArray());
if ($actual !== ['first+tag@example.com', 'second@example.org']) {
    throw new RuntimeException('Admin notification recipients must discard malformed addresses and duplicates.');
}

$reflection->setValue($properties, "\n\r\n");
if ($properties->getRecipientArray() !== []) {
    throw new RuntimeException('Empty recipient rows must not reach the mailer.');
}

echo "PASS: Admin notification recipients are valid and unique.\n";
