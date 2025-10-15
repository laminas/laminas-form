<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\PHPUnit\Set\PHPUnitSetList;

return RectorConfig::configure()
    ->withPhpSets(php82: true)
    ->withSkip([
        /**
         * This breaks tests for FormElementManager initializers.
         *
         * When we upgrade to SMv4 and get rid of initializers, this rule can be enabled again.
         */
        FirstClassCallableRector::class,
    ])
    ->withSets([
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
        PHPUnitSetList::PHPUNIT_100,
        PHPUnitSetList::PHPUNIT_110,
    ])
    ->withPaths([
        __DIR__ . '/../../src',
        __DIR__ . '/../../test',
    ]);
