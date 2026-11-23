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
        $this->assertSame($this->old($input), $this->new($input));
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
        yield 'italic' => ['Hallo [i]schief[/i] Welt'];
        yield 'underline' => ['Hallo [u]unterstrichen[/u] Welt'];
        yield 'underline uppercase' => ['[U]upper[/U]'];
        yield 'underline nested' => ['[u][b]x[/b][/u]'];
        yield 'underline crossing' => ['[u][i]x[/u][/i]'];
        yield 'underline unclosed' => ['[u]text'];
        yield 'underline empty' => ['[u][/u]'];
        yield 'deprecated F is bold' => ['Hallo [F]fett[/F] Welt'];
        yield 'deprecated F in lower case' => ['[f]x[/f]'];
        yield 'deprecated K is italic' => ['Hallo [K]schief[/K] Welt'];
        yield 'deprecated K nested' => ['[F][K]x[/K][/F]'];
        yield 'deprecated F unclosed' => ['[F]text'];
        yield 'tags in code stay raw' => ['[code][u]x[/u][F]y[/F][/code]'];
        yield 'escaped text in underline' => ['[u]<i>&</i>[/u]'];
        yield 'closing tag without opening' => ['x[/u] y[/F]'];
        yield 'option on bold stays literal' => ['[b=x]a[/b]'];
        yield 'option on underline stays literal' => ['[u=x]a[/u]'];
        yield 'option on deprecated F stays literal' => ['[F=x]a[/F]'];
        yield 'bold does not pair with F' => ['[b]x[/F]'];
        yield 'F does not pair with bold' => ['[F]x[/b]'];
        yield 'option on italic stays literal' => ['[i=x]a[/i]'];
        yield 'option on deprecated K stays literal' => ['[K=x]a[/K]'];
        yield 'deprecated K uppercase and lower case' => ['[K]a[/K] [k]b[/k]'];
        yield 'deprecated K empty' => ['[K][/K]'];
        yield 'deprecated K unclosed' => ['[K]text'];
        yield 'deprecated K crossing' => ['[K][F]x[/K][/F]'];
        yield 'F does not pair with K' => ['[F]x[/K]'];
        yield 'K does not pair with F' => ['[K]x[/F]'];
        yield 'underline does not pair with bold' => ['[u]x[/b]'];
        yield 'tag with whitespace only' => ['[b] [/b]'];
        yield 'tag over a soft break' => ["a [b]\nb[/b]"];
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

        $this->assertSame($this->old($input), $normalized);
    }

    /**
     * Documented differences to 1.x.
     *
     * @return iterable<string, array{string, string, string}>
     */
    public static function differenceProvider(): iterable
    {
        yield 'bold renders strong, 1.x renders b' => [
            '[b]x[/b]',
            '<p><b>x</b></p>',
            '<p><strong>x</strong></p>',
        ];
        yield 'italic renders em, 1.x renders i' => [
            '[i]x[/i]',
            '<p><i>x</i></p>',
            '<p><em>x</em></p>',
        ];
        yield 'deprecated F and K render strong and em like their tags' => [
            '[F]x[/F][K]y[/K]',
            '<p><b>x</b><i>y</i></p>',
            '<p><strong>x</strong><em>y</em></p>',
        ];
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
        yield 'underline over a paragraph break, 1.x renders invalid nesting' => [
            "[u]a\n\nb[/u]",
            "<p><u>a</p>\n<p>b</u></p>",
            "<p><u>a</u></p>\n<p>b[/u]</p>",
        ];
    }

    #[DataProvider('differenceProvider')]
    public function testDocumentedDifferences(string $input, string $old, string $new): void
    {
        $this->assertSame($old, $this->old($input));
        $this->assertSame($new, $this->new($input));
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
