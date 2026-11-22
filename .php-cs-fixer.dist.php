<?php

$finder = (new PhpCsFixer\Finder())
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS3x0' => true,
        'blank_line_after_opening_tag' => false, // Not yet: 77 files
        'trailing_comma_in_multiline' => false, // Not yet: 13 files
        'operator_linebreak' => false, // Not yet: 3 files
        'single_line_empty_body' => false, // Not yet: 1 file
    ])
    ->setCacheFile('.php-cs-fixer.cache')
    ->setFinder($finder)
;
