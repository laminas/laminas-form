<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\PHPUnit\PHPUnit60\Rector\ClassMethod\AddDoesNotPerformAssertionToNonAssertingTestRector;

return RectorConfig::configure()
    ->withPhpSets(php82: true)
    ->withSkip([
        /**
         * This breaks tests for FormElementManager initializers.
         *
         * When we upgrade to SMv4 and get rid of initializers, this rule can be enabled again.
         */
        ArrayToFirstClassCallableRector::class,
        /**
         * False positives: several tests assert through dynamic calls like `$this->$assertion(...)`.
         */
        AddDoesNotPerformAssertionToNonAssertingTestRector::class,
    ])
    ->withComposerBased(phpunit: true)
    ->withAttributesSets(phpunit: true)
    ->withPaths([
        __DIR__ . '/../../src',
        __DIR__ . '/../../test',
    ]);
