<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Parser;

use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Parser\BBCodeTreeParser;
use Youthweb\BBCodeParser\Parser\SpikeHtmlRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Back-to-back test of the spike (#45): 1.x engine against the league node tree.
 */
#[CoversClass(BBCodeTreeParser::class)]
#[CoversClass(SpikeHtmlRenderer::class)]
final class BBCodeTreeParserTest extends TestCase
{
    /**
     * Inputs where the structure and the markup are identical to 1.x.
     *
     * @return iterable<string, array{string}>
     */
    public static function identicalProvider(): iterable
    {
        yield 'plain text' => ['text[/b]'];
        yield 'soft break' => ["Zeile1\nZeile2"];
        yield 'paragraphs' => ["A\n\nB"];
        yield 'crlf' => ["A\r\nB"];
        yield 'surrounding blank lines' => ["\n\nA\n"];
        yield 'code raw' => ['[code][b]x[/b][/code]'];
        yield 'code multiline' => ["[code]a\nb[/code]"];
        yield 'code unclosed' => ['[code]open'];
        yield 'code splits paragraph' => ['x [code]y[/code] z'];
        yield 'html is escaped' => ['<b>x</b> & "q"'];
        yield 'unknown tag' => ['[unknown]x[/unknown]'];
    }

    #[DataProvider('identicalProvider')]
    public function testIdenticalToOldEngine(string $input): void
    {
        self::assertSame($this->old($input), $this->new($input));
    }

    /**
     * Inputs where the tree is the same as in 1.x and only the markup of the
     * renderers differs: <strong>/<em> instead of <b>/<i>, no target="_blank".
     *
     * @return iterable<string, array{string}>
     */
    public static function sameStructureProvider(): iterable
    {
        yield 'bold' => ['[b]Hello[/b]'];
        yield 'bold in text' => ['Hallo [b]fett[/b] Welt'];
        yield 'uppercase tags' => ['[B]upper[/B]'];
        yield 'nested' => ['[b][i]x[/i][/b]'];
        yield 'same tag nested' => ['[b][b]x[/b][/b]'];
        yield 'crossing tags' => ['[b][i]x[/b][/i]'];
        yield 'unclosed' => ['[b]text'];
        yield 'empty tag' => ['[b][/b]'];
        yield 'url' => ['[url]http://example.com[/url]'];
        yield 'url with parameter' => ['[url=http://example.com]Link[/url]'];
        yield 'url with quoted parameter' => ['[url="http://a.de"]q[/url]'];
        yield 'url with nested tag' => ['[url=http://a.de][b]x[/b][/url]'];
        yield 'url unclosed' => ['[url=http://a.de]'];
    }

    #[DataProvider('sameStructureProvider')]
    public function testSameStructureAsOldEngine(string $input): void
    {
        $normalized = strtr($this->new($input), [
            '<strong>' => '<b>',
            '</strong>' => '</b>',
            '<em>' => '<i>',
            '</em>' => '</i>',
            '<a href' => '<a target="_blank" href',
        ]);

        self::assertSame($this->old($input), $normalized);
    }

    /**
     * Documented differences to 1.x.
     *
     * @return iterable<string, array{string, string, string}>
     */
    public static function differenceProvider(): iterable
    {
        yield 'unsafe url is dropped, 1.x rewrites it to http://x' => [
            '[url=javascript:alert(1)]x[/url]',
            '<p><a target="_blank" href="http://x">x</a></p>',
            '<p><a>x</a></p>',
        ];
        yield 'url tag without url is a link, 1.x renders text only' => [
            '[url]not a url[/url]',
            '<p>not a url</p>',
            '<p><a href="not a url">not a url</a></p>',
        ];
        yield 'tag over a paragraph break, 1.x renders invalid nesting' => [
            "[b]a\n\nb[/b]",
            "<p><b>a</p>\n<p>b</b></p>",
            "<p><strong>a</strong></p>\n<p>b[/b]</p>",
        ];
    }

    #[DataProvider('differenceProvider')]
    public function testDocumentedDifferences(string $input, string $old, string $new): void
    {
        self::assertSame($old, $this->old($input));
        self::assertSame($new, $this->new($input));
    }

    private function old(string $input): string
    {
        return (new Manager())->parse($input);
    }

    private function new(string $input): string
    {
        return (new SpikeHtmlRenderer())->render((new BBCodeTreeParser())->parse($input));
    }
}
