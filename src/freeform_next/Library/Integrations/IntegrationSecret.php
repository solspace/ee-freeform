<?php

namespace Solspace\Addons\FreeformNext\Library\Integrations;

use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Throwable;

/** Encrypt control-panel credentials with ExpressionEngine's site encryption key. */
final class IntegrationSecret
{
    private const PREFIX = 'ffenc:v1:';

    public static function encrypt(string $value): string
    {
        try {
            $encrypted = ee('Encrypt')->encrypt($value);
            if (!is_string($encrypted) || $encrypted === '') {
                throw new IntegrationException('Encryption failed');
            }
            return self::PREFIX . base64_encode($encrypted);
        } catch (Throwable $e) {
            throw new IntegrationException('Unable to encrypt the credential. Check the ExpressionEngine encryption key.');
        }
    }

    public static function decrypt(string $value): string
    {
        try {
            if (!str_starts_with($value, self::PREFIX)) {
                throw new IntegrationException('Invalid encrypted credential');
            }
            $encrypted = base64_decode(substr($value, strlen(self::PREFIX)), true);
            if ($encrypted === false || $encrypted === '') {
                throw new IntegrationException('Invalid encrypted credential');
            }
            $decrypted = ee('Encrypt')->decrypt($encrypted);
            if (!is_string($decrypted) || $decrypted === '') {
                throw new IntegrationException('Decryption failed');
            }
            return $decrypted;
        } catch (Throwable $e) {
            throw new IntegrationException('Unable to decrypt the saved credential. Re-enter it or restore the original ExpressionEngine encryption key.');
        }
    }
}
