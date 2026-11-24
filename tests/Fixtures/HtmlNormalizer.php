<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures;

/**
 * Evens out the markup differences between 1.x and the league engine that are already decided,
 * so that the back-to-back test shows only the differences that are not.
 *
 * What it does not touch: text, the order of elements and the whitespace inside a paragraph.
 * Every rule is explained in `docs/v2-html-differences.md`.
 */
final class HtmlNormalizer
{
    /** 1.x writes the old presentational tags, the league engine the semantic ones. */
    private const RENAMED = ['b' => 'strong', 'i' => 'em'];

    /** Whitespace around these tags is layout only. */
    private const BLOCKS = 'p|blockquote|cite|ul|ol|li|pre|div|h[1-6]';

    public static function normalize(string $html): string
    {
        $html = str_replace("\r\n", "\n", $html);

        $html = (string) preg_replace_callback(
            '~<(/?)([a-zA-Z][a-zA-Z0-9]*)((?:\s+[^<>]*?)?)\s*(/?)>~',
            static function (array $match): string {
                $name = strtolower($match[2]);
                $name = self::RENAMED[$name] ?? $name;
                $attributes = self::attributes($match[3]);

                if ($match[1] === '/') {
                    return '</' . $name . '>';
                }

                return '<' . $name . $attributes . ($match[4] === '/' ? ' /' : '') . '>';
            },
            $html,
        );

        $html = (string) preg_replace('~\s*(</?(?:' . self::BLOCKS . ')(?:\s[^<>]*)?>)\s*~', '$1', $html);

        return trim($html);
    }

    /**
     * The attributes in alphabetical order, without `target="_blank"`.
     */
    private static function attributes(string $attributes): string
    {
        preg_match_all('~([a-zA-Z-]+)(?:="([^"]*)")?~', $attributes, $found, PREG_SET_ORDER);

        $list = [];

        foreach ($found as $attribute) {
            if ($attribute[1] === 'target' && ($attribute[2] ?? '') === '_blank') {
                continue;
            }

            $list[$attribute[1]] = isset($attribute[2]) ? $attribute[1] . '="' . $attribute[2] . '"' : $attribute[1];
        }

        ksort($list);

        return $list === [] ? '' : ' ' . implode(' ', $list);
    }
}
