<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // Stops at PHP 7.4, the lowest version composer.json allows.
    ->withSets([LevelSetList::UP_TO_PHP_74])
;
