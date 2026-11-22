<?php

$finder = (new PhpCsFixer\Finder())
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS3x0' => true,
        'blank_line_after_opening_tag' => false, // Not yet: 77 files
        'trailing_comma_in_multiline' => false, // Not yet: 13 files. PER-CS 3.0 puts trailing commas in parameter lists, which needs PHP 8.0, so a rollout must configure `elements` without `parameters`
    ])
    ->setCacheFile('.php-cs-fixer.cache')
    ->setFinder($finder)
;
