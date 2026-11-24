<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures;

/**
 * Evens out the markup differences between the parser and the engine that are already decided,
 * so that the back-to-back test shows only the differences that are not.
 *
 * What it does not touch: text, the order of elements and the whitespace inside a paragraph.
 * Every rule is explained in `docs/engine-html-differences.md`.
 */
final class HtmlNormalizer
{
    /** Whitespace next to these tags is layout only. */
    private const BLOCKS = 'p|blockquote|cite|ul|ol|li|pre|div|h[1-6]';

    public static function normalize(string $html): string
    {
        $html = str_replace("\r\n", "\n", $html);

        // The parser writes the old presentational tags, the engine the semantic ones.
        $html = (string) preg_replace(['~<b(?=[\s>/])~', '~</b>~', '~<i(?=[\s>/])~', '~</i>~'], ['<strong', '</strong>', '<em', '</em>'], $html);

        $html = (string) preg_replace('~\s*(</?(?:' . self::BLOCKS . ')(?:\s[^<>]*)?>)\s*~', '$1', $html);

        return trim($html);
    }
}
