<?php

declare(strict_types=1);

/**
 * The cases of the corpus where the league engine differs from 1.x after the normalisation,
 * each with the IDs of the sections in `docs/v2-html-differences.md` that explain why.
 *
 * @return array<string, list<string>> case name (path without extension) => difference IDs
 */
return [
    'block/code-with-html' => ['D08'],
    'block/list-bullet' => ['D03'],
    'block/list-item-inline' => ['D03'],
    'block/list-item-paragraphs' => ['D03', 'D04'],
    'block/list-multiline' => ['D03'],
    'block/list-nested' => ['D01', 'D03'],
    'block/list-text-before-item' => ['D03'],
    'block/list-unclosed' => ['D03'],
    'block/list-with-quote' => ['D01', 'D02', 'D03'],
    'block/many-blank-lines' => ['D05'],
    'block/quote' => ['D01', 'D02'],
    'block/quote-deprecated' => ['D01', 'D02'],
    'block/quote-deprecated-with-cite' => ['D01'],
    'block/quote-empty' => ['D01', 'D02'],
    'block/quote-inline-tags' => ['D01', 'D02'],
    'block/quote-nested' => ['D01', 'D02'],
    'block/quote-paragraphs' => ['D01', 'D02'],
    'block/quote-short-tag' => ['D01', 'D02'],
    'block/quote-short-tag-with-cite' => ['D01'],
    'block/quote-unclosed' => ['D01', 'D02'],
    'block/quote-with-cite' => ['D01'],
    'block/quote-with-cite-quoted' => ['D01'],
    'block/quote-with-list' => ['D01', 'D02', 'D03'],
    'block/quote-with-text-around' => ['D01', 'D02'],
    'block/whitespace-only-line' => ['D05'],
    'broken/list-closed-by-quote' => ['D03'],
    'broken/quote-closed-by-bold' => ['D01', 'D02', 'D06'],
    'broken/tag-over-paragraphs' => ['D06'],
    'broken/tag-over-quote' => ['D01', 'D02', 'D06'],
    'links/autolink-in-bold' => ['D07'],
    'links/autolink-with-ampersand' => ['D08'],
    'nesting/inline-in-url-in-quote' => ['D01', 'D02'],
    'nesting/list-depth-six' => ['D01', 'D03', 'D09'],
    'nesting/list-in-quote-in-list' => ['D01', 'D02', 'D03'],
    'nesting/quote-depth-five' => ['D01', 'D02'],
    'nesting/quote-depth-six' => ['D01', 'D02', 'D09'],
    'special-chars/ampersand' => ['D08'],
    'special-chars/entities' => ['D08'],
];
