<?php

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php71\Rector\BinaryOp\BinaryOpBetweenNumberAndStringRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php73\Rector\ConstFetch\SensitiveConstantNameRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $config): void {
    $config->bootstrapFiles([__DIR__ . '/stubs.php']);

    $config->paths([__DIR__]);

    $config->phpVersion(PhpVersion::PHP_82);

    $config->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::TYPE_DECLARATION,
    ]);

    $config->skip([
        __DIR__ . '/codepack',
        __DIR__ . '/javascript',
        __DIR__ . '/language',
        __DIR__ . '/logs',
        __DIR__ . '/Templates',
        __DIR__ . '/vendor',
        __DIR__ . '/View',
        JsonThrowOnErrorRector::class,
        SensitiveConstantNameRector::class,
        BinaryOpBetweenNumberAndStringRector::class,
        RemoveExtraParametersRector::class,
    ]);

    $config->importNames();
};
