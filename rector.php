<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/test',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets()
    ->withPreparedSets(
        true,
        true,
        true,
        false,
        true,
        false,
        true,
        true,
        true
    )
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
    ]);
