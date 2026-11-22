<?php

use Art4\RectorBcLibrary\Set;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // Stops at PHP 7.4, the lowest version composer.json allows.
    ->withSets([LevelSetList::UP_TO_PHP_74, SetList::DEAD_CODE, SetList::CODE_QUALITY])
    // Strict types change how scalars are coerced, that is no refactoring.
    ->withSkip([SafeDeclareStrictTypesRector::class])
    // Declared types, added through the library's guarded rules: a type on a
    // public or protected member of a non-final class would break subclasses
    // outside this repository (docs/bc-promise.md).
    ->withRules(Set::withTypeCoverageLevel(30))
;
