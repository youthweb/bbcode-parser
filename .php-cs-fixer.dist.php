<?php

$finder = (new PhpCsFixer\Finder())
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'blank_line_after_opening_tag' => false, // Not yet: 77 files
    ])
    ->setCacheFile('.php-cs-fixer.cache')
    ->setFinder($finder)
;
