<?php

use Art4\RectorBcLibrary\Set;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\StmtsAwareInterface\DeclareStrictTypesTestsRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // Stops at PHP 8.2, the lowest version composer.json allows.
    ->withSets([LevelSetList::UP_TO_PHP_82, SetList::DEAD_CODE, SetList::CODE_QUALITY])
    // Rector's PHPUnit code quality set; its rules are fixed ones and none of
    // them needs an API newer than PHPUnit 11 (checked on PHPUnit 11, 12, 13).
    // Left out on purpose: the composer-based PHPUnit set, which follows the
    // installed PHPUnit version and adds #[AllowMockObjectsWithoutExpectations],
    // an attribute that exists since PHPUnit 12.5.2 and not in PHPUnit 11. The
    // narrow-asserts, mock-to-stub and annotations-to-attributes sets find
    // nothing here and are not enabled.
    ->withPreparedSets(phpunitCodeQuality: true)
    // Strict types change how scalars are coerced, that is no refactoring.
    ->withSkip([SafeDeclareStrictTypesRector::class, DeclareStrictTypesTestsRector::class])
    // Declared types, added through the library's guarded rules: a type on a
    // public or protected member of a non-final class would break subclasses
    // outside this repository (docs/bc-promise.md).
    ->withRules(Set::withTypeCoverageLevel(30))
;
