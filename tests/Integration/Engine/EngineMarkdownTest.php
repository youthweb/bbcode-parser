<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Engine;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Engine;
use Youthweb\BBCodeParser\Extension\MarkdownExtension;
use Youthweb\BBCodeParser\Extension\MarkdownTreeListener;
use Youthweb\BBCodeParser\Extension\Parser\DepthLimitedStartParser;
use Youthweb\BBCodeParser\Extension\Renderer\HeadingRenderer;
use Youthweb\BBCodeParser\InputMode;

/**
 * Markdown input of the league engine (#52), tested at its entry point with fixed HTML.
 */
#[CoversClass(Engine::class)]
#[CoversClass(InputMode::class)]
#[CoversClass(MarkdownExtension::class)]
#[CoversClass(MarkdownTreeListener::class)]
#[CoversClass(DepthLimitedStartParser::class)]
#[CoversClass(HeadingRenderer::class)]
final class EngineMarkdownTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function inlineProvider(): iterable
    {
        yield 'plain text' => ['hello', '<p>hello</p>'];
        yield 'strong' => ['**x**', '<p><strong>x</strong></p>'];
        yield 'strong, underscores' => ['__x__', '<p><strong>x</strong></p>'];
        yield 'emphasis' => ['*x*', '<p><em>x</em></p>'];
        yield 'emphasis, underscore' => ['_x_', '<p><em>x</em></p>'];
        yield 'strikethrough' => ['~~x~~', '<p><del>x</del></p>'];
        yield 'inline code' => ['`a < b`', '<p><code>a &lt; b</code></p>'];
        yield 'escaped character' => ['\*x\*', '<p>*x*</p>'];
        yield 'entity' => ['&copy; &amp;', '<p>© &amp;</p>'];
        yield 'two paragraphs' => ["a\n\nb", "<p>a</p>\n<p>b</p>"];
        yield 'soft break' => ["a\nb", '<p>a<br />b</p>'];
        yield 'hard break, two spaces' => ["a  \nb", "<p>a<br />\nb</p>"];
        yield 'hard break, backslash' => ["a\\\nb", "<p>a<br />\nb</p>"];
        yield 'empty input' => ['', ''];
    }

    #[DataProvider('inlineProvider')]
    public function testInline(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->engine()->toHtml($input, InputMode::Markdown));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function blockProvider(): iterable
    {
        yield 'heading 1' => ['# a', '<h3>a</h3>'];
        yield 'heading 4' => ['#### a', '<h6>a</h6>'];
        yield 'heading 6 stops at 6' => ['###### a', '<h6>a</h6>'];
        yield 'setext heading' => ["a\n===", '<h3>a</h3>'];
        yield 'setext heading 2' => ["a\n---", '<h4>a</h4>'];
        yield 'heading with inline' => ['## a **b**', '<h4>a <strong>b</strong></h4>'];
        yield 'thematic break' => ["a\n\n---\n\nb", "<p>a</p>\n<hr />\n<p>b</p>"];
        yield 'quote' => ['> a', "<blockquote title=\"Zitat\">\n<p>a</p>\n</blockquote>"];
        yield 'quote with two paragraphs' => ["> a\n>\n> b", "<blockquote title=\"Zitat\">\n<p>a</p>\n<p>b</p>\n</blockquote>"];
        yield 'bullet list' => ["- a\n- b", "<ul>\n\t<li>a</li>\n\t<li>b</li>\n</ul>"];
        yield 'bullet list, other markers' => ["* a\n* b", "<ul>\n\t<li>a</li>\n\t<li>b</li>\n</ul>"];
        yield 'ordered list' => ["1. a\n2. b", "<ol start=\"1\">\n\t<li>a</li>\n\t<li>b</li>\n</ol>"];
        yield 'ordered list from 3' => ["3. a\n4. b", "<ol start=\"3\">\n\t<li>a</li>\n\t<li>b</li>\n</ol>"];
        yield 'nested list' => ["- a\n  - b", "<ul>\n\t<li>a\n<ul>\n\t<li>b</li>\n</ul></li>\n</ul>"];
        yield 'loose list is rendered like a tight one' => ["- a\n\n- b", "<ul>\n\t<li>a</li>\n\t<li>b</li>\n</ul>"];
        yield 'list item with two paragraphs' => ["- a\n\n  b\n- c", "<ul>\n\t<li><p>a</p>\n<p>b</p></li>\n\t<li>c</li>\n</ul>"];
        yield 'ordered list with a parenthesis' => ["1) a\n2) b", "<ol start=\"1\">\n\t<li>a</li>\n\t<li>b</li>\n</ol>"];
        yield 'other marker starts a new list' => ["- a\n+ b", "<ul>\n\t<li>a</li>\n</ul>\n<ul>\n\t<li>b</li>\n</ul>"];
        yield 'lazy line of a quote' => ["> a\nlazy", "<blockquote title=\"Zitat\">\n<p>a<br />lazy</p>\n</blockquote>"];
        yield 'list with inline' => ['- **a**', "<ul>\n\t<li><strong>a</strong></li>\n</ul>"];
        yield 'fenced code' => ["```\na < b\n```", "<pre><code>a &lt; b</code></pre>"];
        yield 'fenced code with info string' => ["```php\n\$a\n```", "<pre><code>\$a</code></pre>"];
        yield 'indented code' => ["    a < b", "<pre><code>a &lt; b</code></pre>"];
        yield 'code is not parsed' => ["```\n**a**\n```", "<pre><code>**a**</code></pre>"];
    }

    #[DataProvider('blockProvider')]
    public function testBlocks(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->engine()->toHtml($input, InputMode::Markdown));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function linkProvider(): iterable
    {
        yield 'link' => ['[a](https://example.org)', '<p><a target="_blank" href="https://example.org">a</a></p>'];
        yield 'link with title, the title is dropped' => ['[a](https://example.org "t")', '<p><a target="_blank" href="https://example.org">a</a></p>'];
        yield 'link with inline' => ['[**a**](https://example.org)', '<p><a target="_blank" href="https://example.org"><strong>a</strong></a></p>'];
        yield 'reference link' => ["[a][r]\n\n[r]: https://example.org", '<p><a target="_blank" href="https://example.org">a</a></p>'];
        yield 'angle autolink' => ['<https://example.org>', '<p><a target="_blank" href="https://example.org">https://example.org</a></p>'];
        yield 'bare url' => ['see https://example.org now', '<p>see <a target="_blank" href="https://example.org">https://example.org</a> now</p>'];
        yield 'unsafe link shows the text' => ['[a](javascript:alert(1))', '<p>a</p>'];
        yield 'unsafe image shows the text' => ['![a](javascript:alert(1))', '<p>javascript:alert(1)</p>'];
        yield 'image with a data url shows the text' => ['![a](data:image/png;base64,AAAA)', '<p>data:image/png;base64,AAAA</p>'];
        yield 'definition without a use renders nothing' => ['[r]: https://example.org', ''];
        yield 'image' => ['![alt](https://example.org/a.png)', '<p><a target="_blank" href="https://example.org/a.png"><img class="img-responsive" border="0" src="https://example.org/a.png" alt="a" /></a></p>'];
        yield 'image in link' => ['[![alt](https://example.org/a.png)](https://example.org)', '<p><a target="_blank" href="https://example.org"><img class="img-responsive" border="0" src="https://example.org/a.png" alt="a" /></a></p>'];
    }

    #[DataProvider('linkProvider')]
    public function testLinksAndImages(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->engine()->toHtml($input, InputMode::Markdown));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function rawHtmlProvider(): iterable
    {
        yield 'inline tag' => ['<b>x</b>', '<p>&lt;b&gt;x&lt;/b&gt;</p>'];
        yield 'block tag' => ['<div>x</div>', '<p>&lt;div&gt;x&lt;/div&gt;</p>'];
        yield 'comment' => ['<!-- x -->', '<p>&lt;!-- x --&gt;</p>'];
        yield 'quotes and ampersand' => ['a "b" & c', '<p>a &quot;b&quot; &amp; c</p>'];
        yield 'in link text' => ['[<b>](https://example.org)', '<p><a target="_blank" href="https://example.org">&lt;b&gt;</a></p>'];
        yield 'in code' => ['`<b>`', '<p><code>&lt;b&gt;</code></p>'];
    }

    #[DataProvider('rawHtmlProvider')]
    public function testRawHtmlIsEscaped(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->engine()->toHtml($input, InputMode::Markdown));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function parityProvider(): iterable
    {
        yield 'strong' => ['**a**', '[b]a[/b]'];
        yield 'emphasis' => ['*a*', '[i]a[/i]'];
        yield 'text with special characters' => ['<b> & "a"', '<b> & "a"'];
        yield 'link' => ['[a](https://example.org)', '[url=https://example.org]a[/url]'];
        yield 'link with formatted text' => ['[**a**](https://example.org)', '[url=https://example.org][b]a[/b][/url]'];
        yield 'bare url' => ['https://example.org', 'https://example.org'];
        yield 'image' => ['![a](https://example.org/a.png)', '[img]https://example.org/a.png[/img]'];
        yield 'image in link' => ['[![a](https://example.org/a.png)](https://example.org)', '[url=https://example.org][img]https://example.org/a.png[/img][/url]'];
        yield 'quote' => ['> a', '[quote]a[/quote]'];
        yield 'quote with formatted text' => ['> **a**', '[quote][b]a[/b][/quote]'];
        yield 'bullet list' => ["- a\n- b", '[list][*]a[*]b[/list]'];
        yield 'ordered list' => ["1. a\n2. b", '[list=1][*]a[*]b[/list]'];
        yield 'ordered list from 3' => ["3. a\n4. b", '[list=3][*]a[*]b[/list]'];
        yield 'nested list' => ["- a\n  - b", '[list][*]a[list][*]b[/list][/list]'];
        yield 'list of quotes' => ["- > a", '[list][*][quote]a[/quote][/list]'];
        yield 'quote with list' => ["> - a\n> - b", '[quote][list][*]a[*]b[/list][/quote]'];
        yield 'code' => ["```\na < b\n```", '[code]a < b[/code]'];
        yield 'indented code' => ["    a < b", '[code]a < b[/code]'];
        yield 'two paragraphs' => ["a\n\nb", "a\n\nb"];
    }

    #[DataProvider('parityProvider')]
    public function testMarkdownAndBBCodeGiveTheSameHtml(string $markdown, string $bbcode): void
    {
        $engine = $this->engine();

        $this->assertSame($engine->toHtml($bbcode, InputMode::BBCode), $engine->toHtml($markdown, InputMode::Markdown));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string, string}>
     */
    public static function configProvider(): iterable
    {
        yield 'headings off: atx' => [['markdown' => ['headings' => false]], '# a', '<p># a</p>'];
        yield 'headings off: setext' => [['markdown' => ['headings' => false]], "a\n===", '<p>a<br />===</p>'];
        yield 'headings off: the thematic break stays' => [['markdown' => ['headings' => false]], "a\n\n---", "<p>a</p>\n<hr />"];
        yield 'thematic breaks off' => [['markdown' => ['thematic_breaks' => false]], "a\n\n---\n\nb", "<p>a</p>\n<p>---</p>\n<p>b</p>"];
        yield 'thematic breaks off: the headings stay' => [['markdown' => ['thematic_breaks' => false]], "# a\n\nb\n---", "<h3>a</h3>\n<h4>b</h4>"];
        yield 'both off' => [['markdown' => ['headings' => false, 'thematic_breaks' => false]], "# a\n\n---\n\nb\n===", "<p># a</p>\n<p>---</p>\n<p>b<br />===</p>"];
        yield 'heading offset 0' => [['heading_offset' => 0], "# a\n\n###### b", "<h1>a</h1>\n<h6>b</h6>"];
        yield 'heading offset 1' => [['heading_offset' => 1], '# a', '<h2>a</h2>'];
        yield 'heading offset stops at 6' => [['heading_offset' => 5], "# a\n\n## b\n\n### c", "<h6>a</h6>\n<h6>b</h6>\n<h6>c</h6>"];
        yield 'negative heading offset is 0' => [['heading_offset' => -2], '## a', '<h2>a</h2>'];
        yield 'soft break with a newline' => [['soft_break' => "\n"], "a\nb", "<p>a\nb</p>"];
        yield 'soft break as a space' => [['soft_break' => ' '], "a\nb", '<p>a b</p>'];
        yield 'hard break is not configurable' => [['soft_break' => ' '], "a  \nb", "<p>a<br />\nb</p>"];
        yield 'urls are not linked' => [['parse_urls' => false], 'https://example.org', '<p>https://example.org</p>'];
        yield 'short urls' => [['callbacks' => ['url_content' => ['short_url' => true, 'short_url_length' => 20]]], 'https://example.org/a/very/long/path', '<p><a target="_blank" href="https://example.org/a/very/long/path">example.org/a/very…</a></p>'];
        yield 'no target' => [['callbacks' => ['url_content' => ['target' => null]]], '[a](https://example.org)', '<p><a href="https://example.org">a</a></p>'];
    }

    /**
     * @param array<string, mixed> $config
     */
    #[DataProvider('configProvider')]
    public function testConfig(array $config, string $input, string $expected): void
    {
        $this->assertSame($expected, $this->engine($config)->toHtml($input, InputMode::Markdown));
    }

    public function testConfigOfTheMarkdownModeDoesNotChangeTheBBCodeMode(): void
    {
        $engine = $this->engine(['soft_break' => ' ', 'heading_offset' => 0, 'markdown' => ['headings' => false]]);

        $this->assertSame("<p>a<br />\nb</p>", $engine->toHtml("a\nb", InputMode::BBCode));
    }

    public function testTheModesCanBeUsedOneAfterTheOther(): void
    {
        $engine = $this->engine();

        $this->assertSame('<p><em>a</em></p>', $engine->toHtml('*a*', InputMode::Markdown));
        $this->assertSame('<p><em>a</em></p>', $engine->toHtml('[i]a[/i]', InputMode::BBCode));
        $this->assertSame('<p><em>b</em></p>', $engine->toHtml('*b*', InputMode::Markdown));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function depthProvider(): iterable
    {
        $open = '<blockquote title="Zitat">';
        $close = '</blockquote>';

        yield 'five quotes' => ['> > > > > a', str_repeat($open . "\n", 5) . "<p>a</p>\n" . str_repeat($close . "\n", 4) . $close];
        yield 'the sixth quote stays text' => ['> > > > > > a', str_repeat($open . "\n", 5) . "<p>&gt; a</p>\n" . str_repeat($close . "\n", 4) . $close];
        yield 'five lists' => [
            "- a\n  - b\n    - c\n      - d\n        - e",
            "<ul>\n\t<li>a\n<ul>\n\t<li>b\n<ul>\n\t<li>c\n<ul>\n\t<li>d\n<ul>\n\t<li>e</li>\n</ul></li>\n</ul></li>\n</ul></li>\n</ul></li>\n</ul>",
        ];
        yield 'the sixth list stays text' => [
            "- a\n  - b\n    - c\n      - d\n        - e\n          - f",
            "<ul>\n\t<li>a\n<ul>\n\t<li>b\n<ul>\n\t<li>c\n<ul>\n\t<li>d\n<ul>\n\t<li>e<br />- f</li>\n</ul></li>\n</ul></li>\n</ul></li>\n</ul></li>\n</ul>",
        ];
        yield 'a second item on the fifth list level' => [
            "- a\n  - b\n    - c\n      - d\n        - e\n        - f",
            "<ul>\n\t<li>a\n<ul>\n\t<li>b\n<ul>\n\t<li>c\n<ul>\n\t<li>d\n<ul>\n\t<li>e</li>\n\t<li>f</li>\n</ul></li>\n</ul></li>\n</ul></li>\n</ul></li>\n</ul>",
        ];
        yield 'a list after the fifth quote level' => [
            "> > > > > a\n\n- b",
            str_repeat($open . "\n", 5) . "<p>a</p>\n" . str_repeat($close . "\n", 4) . $close . "\n<ul>\n\t<li>b</li>\n</ul>",
        ];
        yield 'quotes and lists add up: five levels' => [
            '> - > - > a',
            "<blockquote title=\"Zitat\">\n<ul>\n\t<li><blockquote title=\"Zitat\">\n<ul>\n\t<li><blockquote title=\"Zitat\">\n<p>a</p>\n</blockquote></li>\n</ul>\n</blockquote></li>\n</ul>\n</blockquote>",
        ];
        yield 'quotes and lists add up: the sixth level stays text' => [
            '> - > - > - a',
            "<blockquote title=\"Zitat\">\n<ul>\n\t<li><blockquote title=\"Zitat\">\n<ul>\n\t<li><blockquote title=\"Zitat\">\n<p>- a</p>\n</blockquote></li>\n</ul>\n</blockquote></li>\n</ul>\n</blockquote>",
        ];
    }

    #[DataProvider('depthProvider')]
    public function testNestingIsLimitedToFiveLevels(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->engine()->toHtml($input, InputMode::Markdown));
    }

    public function testBBCodeIsTheDefaultMode(): void
    {
        $this->assertSame('<p><strong>x</strong></p>', $this->engine()->toHtml('[b]x[/b]'));
        $this->assertSame('<p><strong>x</strong></p>', (new Engine(new Config()))->toHtml('[b]x[/b]', InputMode::BBCode));
    }

    public function testBBCodeTagsAreTextInMarkdownMode(): void
    {
        $this->assertSame('<p>[b]x[/b]</p>', $this->engine()->toHtml('[b]x[/b]', InputMode::Markdown));
    }

    public function testMarkdownIsTextInBBCodeMode(): void
    {
        $this->assertSame('<p>**x**</p>', $this->engine()->toHtml('**x**', InputMode::BBCode));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function engine(array $config = []): Engine
    {
        // The image check would ask the network.
        $config['callbacks']['image']['force_check'] ??= false;

        $object = new Config();
        $object->mergeRecursive($config);

        return new Engine($object);
    }
}
