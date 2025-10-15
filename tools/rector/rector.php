<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;

return RectorConfig::configure()
    //->withPhpSets(php82: true)
    ->withSets([
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
        PHPUnitSetList::PHPUNIT_100,
        PHPUnitSetList::PHPUNIT_110,
    ])
    ->withPaths([
        __DIR__ . '/../../src',
        __DIR__ . '/../../test',
    ]);
