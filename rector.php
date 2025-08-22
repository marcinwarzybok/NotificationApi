<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPHPStanConfigs([__DIR__.'/phpstan.dist.neon'])
    ->withPhpSets(php84: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        strictBooleans: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withComposerBased(
        doctrine: true,
        symfony: true,
    )
    ->withSkip([
        ReadOnlyPropertyRector::class => [
            __DIR__.'/src/EmailNotification/Shared/Model/EmailNotification.php',
            __DIR__.'/src/EmailNotification/Shared/Model/Notification.php',
        ],
    ])
    ->withAttributesSets(symfony: true);
