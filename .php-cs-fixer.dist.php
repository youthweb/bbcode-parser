<?php

$finder = (new PhpCsFixer\Finder())
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'binary_operator_spaces' => false, // Not yet: 1 file
        'blank_line_after_opening_tag' => false, // Not yet: 77 files
    ])
    ->setCacheFile('.php-cs-fixer.cache')
    ->setFinder($finder)
;
