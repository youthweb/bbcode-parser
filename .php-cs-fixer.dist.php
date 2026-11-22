<?php

$finder = (new PhpCsFixer\Finder())
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS3x0' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'array_destructuring', 'arrays']],
    ])
    ->setCacheFile('.php-cs-fixer.cache')
    ->setFinder($finder)
;
