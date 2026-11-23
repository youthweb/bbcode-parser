<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Parser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Extension\BBCodeExtension;
use Youthweb\BBCodeParser\Extension\Node\BBList;
use Youthweb\BBCodeParser\Extension\Node\BBListItem;
use Youthweb\BBCodeParser\Extension\Renderer\ListRenderer;
use Youthweb\BBCodeParser\Extension\Renderer\QuoteRenderer;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Parser\BBCodeTreeParser;
use Youthweb\BBCodeParser\Parser\BlockTreeBuilder;
use Youthweb\BBCodeParser\Parser\SpikeHtmlRenderer;

/**
 * Back-to-back test of the list, quote and noparse tags (#50): 1.x engine against the league node tree.
 *
 * The new engine renders valid HTML, so where 1.x does not, the difference is documented here.
 */
#[CoversClass(BBCodeTreeParser::class)]
#[CoversClass(BlockTreeBuilder::class)]
#[CoversClass(SpikeHtmlRenderer::class)]
#[CoversClass(BBCodeExtension::class)]
#[CoversClass(BBList::class)]
#[CoversClass(BBListItem::class)]
#[CoversClass(ListRenderer::class)]
#[CoversClass(QuoteRenderer::class)]
final class BBCodeBlockTagsTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function identicalProvider(): iterable
    {
        yield 'list' => ['[list][*]a[*]b[/list]'];
        yield 'list with inline tags' => ['[list][*][b]a[/b][*]c[/list]'];
        yield 'ordered list' => ['[list=1][*]a[*]b[/list]'];
        yield 'ordered list from 5' => ['[list=5][*]a[/list]'];
        yield 'ordered list with a negative start' => ['[list=-3][*]a[/list]'];
        yield 'list with letters' => ['[list=a][*]a[/list]'];
        yield 'list with capital letters' => ['[list=A][*]a[/list]'];
        yield 'list with roman numbers' => ['[list=i][*]a[/list]'];
        yield 'list with capital roman numbers' => ['[list=I][*]a[/list]'];
        yield 'list with an unknown option' => ['[list=x][*]a[/list]'];
        yield 'list with a quoted option' => ['[list="1"][*]a[/list]'];
        yield 'text before the first item is the first item' => ['[list]a[*]b[/list]'];
        yield 'empty list' => ['[list][/list]'];
        yield 'list with blanks only' => ["[list]  \n [/list]"];
        yield 'unclosed list' => ['[list][*]a'];
        yield 'uppercase list' => ['[LIST][*]a[/LIST]'];
        yield 'noparse' => ['[noparse][b]x[/b] <i>[/noparse]'];
        yield 'noparse unclosed' => ['[noparse][b]x'];
        yield 'noparse with option stays text' => ['[noparse=x]a[/noparse]'];
        yield 'noparse around code' => ['[noparse][code]x[/code][/noparse]'];
        yield 'code around noparse' => ['[code][noparse]x[/noparse][/code]'];
        yield 'empty noparse' => ['[noparse][/noparse]'];
        yield 'noparse in a text' => ['a [noparse][b][/noparse] b'];
        yield 'noparse with an ampersand' => ['[noparse]a & b[/noparse]'];
        yield 'uppercase noparse' => ['[NOPARSE][b][/NOPARSE]'];
        yield 'noparse with an url' => ['[noparse]http://a.de[/noparse]'];
    }

    #[DataProvider('identicalProvider')]
    public function testIdenticalToOldEngine(string $input): void
    {
        $this->assertSame($this->old($input), $this->new($input));
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function differenceProvider(): iterable
    {
        yield 'quote is no child of a paragraph and has no empty cite' => [
            '[quote]hi[/quote]',
            '<p><blockquote title="Zitat"><cite></cite>hi</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<p>hi</p>\n</blockquote>",
        ];
        yield 'quote with an option has a cite' => [
            '[quote=Max]hi[/quote]',
            '<p><blockquote title="Zitat"><cite>Max</cite>hi</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<cite>Max</cite>\n<p>hi</p>\n</blockquote>",
        ];
        yield 'q is a quote' => [
            '[q=Max]hi[/q]',
            '<p><blockquote title="Zitat"><cite>Max</cite>hi</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<cite>Max</cite>\n<p>hi</p>\n</blockquote>",
        ];
        yield 'deprecated Z is a quote' => [
            '[Z=Max]hi[/Z]',
            '<p><blockquote title="Zitat"><cite>Max</cite>hi</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<cite>Max</cite>\n<p>hi</p>\n</blockquote>",
        ];
        yield 'the option of a quote is read up to the bracket, spaces included' => [
            '[quote=Max Mustermann]hi[/quote]',
            '<p><blockquote title="Zitat"><cite>Max</cite>hi</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<cite>Max Mustermann</cite>\n<p>hi</p>\n</blockquote>",
        ];
        yield 'quote with an empty option has no cite' => [
            '[quote=]hi[/quote]',
            '<p><blockquote title="Zitat"><cite></cite>hi</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<p>hi</p>\n</blockquote>",
        ];
        yield 'quote with inline tags' => [
            '[quote=Max][b]fett[/b] und [url]http://a.de[/url][/quote]',
            '<p><blockquote title="Zitat"><cite>Max</cite><b>fett</b> und <a target="_blank" href="http://a.de">http://a.de</a></blockquote></p>',
            "<blockquote title=\"Zitat\">\n<cite>Max</cite>\n<p><b>fett</b> und <a target=\"_blank\" href=\"http://a.de\">http://a.de</a></p>\n</blockquote>",
        ];
        yield 'quote splits the paragraph' => [
            'text[quote]a[/quote]text',
            '<p>text<blockquote title="Zitat"><cite></cite>a</blockquote>text</p>',
            "<p>text</p>\n<blockquote title=\"Zitat\">\n<p>a</p>\n</blockquote>\n<p>text</p>",
        ];
        yield 'blank line in a quote gives paragraphs, 1.x renders invalid nesting' => [
            "[quote=Max]a\n\nb[/quote]",
            "<p><blockquote title=\"Zitat\"><cite>Max</cite>a</p>\n<p>b</blockquote></p>",
            "<blockquote title=\"Zitat\">\n<cite>Max</cite>\n<p>a</p>\n<p>b</p>\n</blockquote>",
        ];
        yield 'nested quotes have the cite first' => [
            '[quote=A]x[quote=B]y[/quote]z[/quote]',
            '<p><blockquote title="Zitat"><cite>A</cite>x<blockquote title="Zitat"><cite>B</cite>y</blockquote>z</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<cite>A</cite>\n<p>x</p>\n<blockquote title=\"Zitat\">\n<cite>B</cite>\n<p>y</p>\n</blockquote>\n<p>z</p>\n</blockquote>",
        ];
        yield 'quote starting with a quote, 1.x puts the cite behind the inner quote' => [
            '[quote=A][quote=B]y[/quote][/quote]',
            '<p><blockquote title="Zitat"><blockquote title="Zitat"><cite>B</cite>y</blockquote><cite>A</cite></blockquote></p>',
            "<blockquote title=\"Zitat\">\n<cite>A</cite>\n<blockquote title=\"Zitat\">\n<cite>B</cite>\n<p>y</p>\n</blockquote>\n</blockquote>",
        ];
        yield 'unclosed quote' => [
            '[quote]',
            '<p><blockquote title="Zitat"><cite></cite></blockquote></p>',
            '<blockquote title="Zitat"></blockquote>',
        ];
        yield 'a quote closes only with its own tag' => [
            '[quote=Max]a[/q]b',
            '<p><blockquote title="Zitat"><cite>Max</cite>a[/q]b</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<cite>Max</cite>\n<p>a[/q]b</p>\n</blockquote>",
        ];
        yield 'quote closes the lists inside' => [
            '[quote]a[list][*]x[/quote][/list]',
            "<p><blockquote title=\"Zitat\"><cite></cite>a</p>\n<ul type=\"disc\">\n\t<li>x</li>\n</ul>\n\n<p></blockquote>[/list]</p>",
            "<blockquote title=\"Zitat\">\n<p>a</p>\n<ul type=\"disc\">\n\t<li>x</li>\n</ul>\n</blockquote>\n<p>[/list]</p>",
        ];
        yield 'list in a quote' => [
            '[quote][list][*]a[/list][/quote]',
            "<p><blockquote title=\"Zitat\"><cite></cite></p>\n<ul type=\"disc\">\n\t<li>a</li>\n</ul>\n\n<p></blockquote></p>",
            "<blockquote title=\"Zitat\">\n<ul type=\"disc\">\n\t<li>a</li>\n</ul>\n</blockquote>",
        ];
        yield 'quote in a list item' => [
            '[list][*][quote]q[/quote][/list]',
            "<ul type=\"disc\">\n\t<li><blockquote title=\"Zitat\"><cite></cite>q</blockquote></li>\n</ul>",
            "<ul type=\"disc\">\n\t<li><blockquote title=\"Zitat\">\n<p>q</p>\n</blockquote></li>\n</ul>",
        ];
        yield 'list splits the paragraph' => [
            "vor\n[list][*]a[/list]\nnach",
            "<p>vor</p>\n<ul type=\"disc\">\n\t<li>a</li>\n</ul>\n\n<p>nach</p>",
            "<p>vor</p>\n<ul type=\"disc\">\n\t<li>a</li>\n</ul>\n<p>nach</p>",
        ];
        yield 'new line in a list item is a line break' => [
            "[list][*]a\nb[*]c[/list]",
            "<ul type=\"disc\">\n\t<li>a\nb</li>\n\t<li>c</li>\n</ul>",
            "<ul type=\"disc\">\n\t<li>a<br />\nb</li>\n\t<li>c</li>\n</ul>",
        ];
        yield 'blank line in a list item gives paragraphs' => [
            "[list][*]a\n\nb[*]c[/list]",
            "<ul type=\"disc\">\n\t<li>a\n\nb</li>\n\t<li>c</li>\n</ul>",
            "<ul type=\"disc\">\n\t<li><p>a</p>\n<p>b</p></li>\n\t<li>c</li>\n</ul>",
        ];
        yield 'nested list, 1.x renders a list in a paragraph' => [
            '[list][*]a[list][*]x[*]y[/list][*]b[/list]',
            "<ul type=\"disc\">\n\t<li>a\n<p><ul type=\"disc\"><br />\n\t<li>x</li><br />\n\t<li>y</li><br />\n</ul></p>\n</li>\n\t<li>b</li>\n</ul>",
            "<ul type=\"disc\">\n\t<li>a\n<ul type=\"disc\">\n\t<li>x</li>\n\t<li>y</li>\n</ul></li>\n\t<li>b</li>\n</ul>",
        ];
        yield 'item marker outside of a list stays text' => [
            '[list][*]a[/list][*]b',
            "<ul type=\"disc\">\n\t<li>a</li>\n</ul>\n\n<p>[*]b</p>",
            "<ul type=\"disc\">\n\t<li>a</li>\n</ul>\n<p>[*]b</p>",
        ];
        yield 'noparse keeps a break in one paragraph, 1.x splits the paragraph' => [
            "a [noparse]\n[b]\n\n[b][/noparse] b",
            "<p>a <br />\n[b]</p>\n<p>[b] b</p>",
            "<p>a <br />\n[b]<br />\n<br />\n[b] b</p>",
        ];
        yield 'noparse hides block tags' => [
            '[quote][noparse][/quote][/noparse][/quote]',
            '<p><blockquote title="Zitat"><cite></cite>[/quote]</blockquote></p>',
            "<blockquote title=\"Zitat\">\n<p>[/quote]</p>\n</blockquote>",
        ];
        yield 'inline tag around a block ends at the block' => [
            '[b][quote]x[/quote][/b]',
            '<p><b><blockquote title="Zitat"><cite></cite>x</blockquote></b></p>',
            "<p><b></b></p>\n<blockquote title=\"Zitat\">\n<p>x</p>\n</blockquote>\n<p>[/b]</p>",
        ];
    }

    #[DataProvider('differenceProvider')]
    public function testDocumentedDifferences(string $input, string $old, string $new): void
    {
        $this->assertSame($old, $this->old($input));
        $this->assertSame($new, $this->new($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function newEngineProvider(): iterable
    {
        yield 'the option of a quote is escaped' => [
            '[quote=<svg/onload=alert(1)>]x[/quote]',
            "<blockquote title=\"Zitat\">\n<cite>&lt;svg/onload=alert(1)&gt;</cite>\n<p>x</p>\n</blockquote>",
        ];
        yield 'the option of a quote is no markup' => [
            '[quote=a&b "c" d]x[/quote]',
            "<blockquote title=\"Zitat\">\n<cite>a&amp;b &quot;c&quot; d</cite>\n<p>x</p>\n</blockquote>",
        ];
        yield 'the option of a list is no markup' => [
            '[list=<svg/onload=alert(1)>][*]a[/list]',
            "<ul type=\"disc\">\n\t<li>a</li>\n</ul>",
        ];
        yield 'a list item with a list and a paragraph has the paragraph tight' => [
            '[list][*]a[list=1][*]x[/list][/list]',
            "<ul type=\"disc\">\n\t<li>a\n<ol start=\"1\">\n\t<li>x</li>\n</ol></li>\n</ul>",
        ];
        yield 'a quote in the middle of a list item' => [
            "[list][*]a\n\n[quote]q[/quote]\n\nb[/list]",
            "<ul type=\"disc\">\n\t<li><p>a</p>\n<blockquote title=\"Zitat\">\n<p>q</p>\n</blockquote>\n<p>b</p></li>\n</ul>",
        ];
        yield 'the first item marker does not leave an empty item' => [
            '[list]  [*]a[*][*]b[/list]',
            "<ul type=\"disc\">\n\t<li>a</li>\n\t<li></li>\n\t<li>b</li>\n</ul>",
        ];
        yield 'item marker in a quote in a list item stays text' => [
            '[list][*][quote]a[*]b[/quote][/list]',
            "<ul type=\"disc\">\n\t<li><blockquote title=\"Zitat\">\n<p>a[*]b</p>\n</blockquote></li>\n</ul>",
        ];
        yield 'code in a quote' => [
            '[quote][code]x[/code][/quote]',
            "<blockquote title=\"Zitat\">\n<pre><code>x</code></pre>\n</blockquote>",
        ];
        yield 'code in a list item' => [
            '[list][*][code]x[/code][/list]',
            "<ul type=\"disc\">\n\t<li><pre><code>x</code></pre></li>\n</ul>",
        ];
        yield 'noparse in an inline tag' => [
            '[b]a [noparse][i][/noparse] b[/b]',
            '<p><b>a [i] b</b></p>',
        ];
        yield 'noparse before code wins and code stays text' => [
            '[noparse][code][/noparse]x[/code]',
            "<p>[code]x[/code]</p>",
        ];
        yield 'stray closing tags stay text' => [
            '[/quote][/list][/noparse]',
            '<p>[/quote][/list][/noparse]</p>',
        ];
        yield 'five nested levels are rendered' => [
            '[quote][quote][quote][quote][quote]x[/quote][/quote][/quote][/quote][/quote]',
            "<blockquote title=\"Zitat\">\n<blockquote title=\"Zitat\">\n<blockquote title=\"Zitat\">\n<blockquote title=\"Zitat\">\n<blockquote title=\"Zitat\">\n<p>x</p>\n</blockquote>\n</blockquote>\n</blockquote>\n</blockquote>\n</blockquote>",
        ];
        yield 'the sixth level stays text, its closing tag too' => [
            '[quote][list][quote][list][quote][quote]x[/quote][/quote][/list][/quote][/list][/quote]',
            "<blockquote title=\"Zitat\">\n<ul type=\"disc\">\n\t<li><blockquote title=\"Zitat\">\n<ul type=\"disc\">\n\t<li><blockquote title=\"Zitat\">\n<p>[quote]x[/quote]</p>\n</blockquote></li>\n</ul>\n</blockquote></li>\n</ul>\n</blockquote>",
        ];
        yield 'a control character of the input is dropped' => [
            "[noparse]a\u{1B}0\u{1B}b[/noparse]",
            '<p>a0b</p>',
        ];
    }

    #[DataProvider('newEngineProvider')]
    public function testNewEngine(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->new($input));
    }

    private function old(string $input): string
    {
        return (new Manager())->parse($input);
    }

    private function new(string $input): string
    {
        $html = (new SpikeHtmlRenderer())->render((new BBCodeTreeParser())->parse($input));

        // The markup of the inline tags is covered by BBCodeTreeParserTest
        return strtr($html, ['<strong>' => '<b>', '</strong>' => '</b>']);
    }
}
