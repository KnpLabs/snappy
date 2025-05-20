<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

return RectorConfig::configure()
    ->withPaths(
        [
            __DIR__.'/src',
        ]
    )
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
    )
    ->withComposerBased(
        phpunit: true,
        symfony: true,
    )
    ->withPhpSets(
        php81: true,
    )
    ->withSkip(
        [
            PreferPHPUnitThisCallRector::class,
            RenameParamToMatchTypeRector::class,
            RenamePropertyToMatchTypeRector::class,
            RenameVariableToMatchMethodCallReturnTypeRector::class,
            RenameVariableToMatchNewTypeRector::class,
        ]
    )
;
