<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Parser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Engine;
use Youthweb\BBCodeParser\Extension\BBCodeExtension;
use Youthweb\BBCodeParser\InputMode;
use Youthweb\BBCodeParser\Extension\Node\BBHeading;
use Youthweb\BBCodeParser\Extension\Renderer\BBHeadingRenderer;
use Youthweb\BBCodeParser\Parser\BBCodeTreeParser;
use Youthweb\BBCodeParser\Parser\BlockTreeBuilder;
use Youthweb\BBCodeParser\Parser\SpikeHtmlRenderer;

/**
 * The tags `[h1]` to `[h6]` and the option `parse_headlines` in the engine (#142).
 */
#[CoversClass(Engine::class)]
#[CoversClass(BBCodeTreeParser::class)]
#[CoversClass(BlockTreeBuilder::class)]
#[CoversClass(SpikeHtmlRenderer::class)]
#[CoversClass(BBCodeExtension::class)]
#[CoversClass(BBHeading::class)]
#[CoversClass(BBHeadingRenderer::class)]
final class BBCodeHeadlineTagsTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}> input, html with the option `parse_headlines`
     */
    public static function headlineProvider(): iterable
    {
        yield 'h1' => ['[h1]T[/h1]', '<h1>T</h1>'];
        yield 'h6' => ['[h6]T[/h6]', '<h6>T</h6>'];
        yield 'every level' => ['[h3]a[/h3]', '<h3>a</h3>'];
        yield 'the level is not moved' => ['[h1]T[/h1]', '<h1>T</h1>'];
        yield 'uppercase' => ['[H2]T[/H2]', '<h2>T</h2>'];
        yield 'text after the heading is a paragraph' => ["[h2]Title[/h2]\ntext", "<h2>Title</h2>\n<p>text</p>"];
        yield 'text before the heading is a paragraph' => ["text\n[h2]Title[/h2]", "<p>text</p>\n<h2>Title</h2>"];
        yield 'text around the heading' => ['a[h2]T[/h2]b', "<p>a</p>\n<h2>T</h2>\n<p>b</p>"];
        yield 'two headings' => ["[h1]a[/h1]\n[h2]b[/h2]", "<h1>a</h1>\n<h2>b</h2>"];
        yield 'inline tags' => ['[h2][b]a[/b] [i]b[/i][/h2]', '<h2><strong>a</strong> <em>b</em></h2>'];
        yield 'link' => ['[h2][url]http://example.org[/url][/h2]', '<h2><a target="_blank" href="http://example.org">http://example.org</a></h2>'];
        yield 'bare url' => ['[h2]http://example.org[/h2]', '<h2><a target="_blank" href="http://example.org">http://example.org</a></h2>'];
        yield 'noparse' => ['[h2][noparse][b][/noparse][/h2]', '<h2>[b]</h2>'];
        yield 'html is escaped' => ['[h2]<b>&</b>[/h2]', '<h2>&lt;b&gt;&amp;&lt;/b&gt;</h2>'];
        yield 'line break' => ["[h2]a\nb[/h2]", "<h2>a<br />\nb</h2>"];
        yield 'blank line is one line break' => ["[h2]a\n\n\nb[/h2]", "<h2>a<br />\nb</h2>"];
        yield 'empty heading renders nothing' => ['[h2][/h2]', ''];
        yield 'blank heading renders nothing' => ["[h2] \n [/h2]", ''];
        yield 'in a quote' => ['[quote][h2]T[/h2][/quote]', "<blockquote title=\"Zitat\">\n<h2>T</h2>\n</blockquote>"];
        yield 'in a list item' => ['[list][*][h2]T[/h2][*]b[/list]', "<ul>\n\t<li><h2>T</h2></li>\n\t<li>b</li>\n</ul>"];
        yield 'quote in a heading stays text' => ['[h2][quote]a[/quote][/h2]', '<h2>[quote]a[/quote]</h2>'];
        yield 'list in a heading stays text' => ['[h2][list][*]a[/list][/h2]', '<h2>[list][*]a[/list]</h2>'];
        yield 'code in a heading stays text' => ['[h2][code]a[/code][/h2]', '<h2>[code]a[/code]</h2>'];
        yield 'heading in a heading stays text' => ['[h1][h2]a[/h2][/h1]', '<h1>[h2]a[/h2]</h1>'];
        yield 'inline tag is closed at the heading' => ['[b][h1]x[/h1][/b]', "<p><strong></strong></p>\n<h1>x</h1>\n<p>[/b]</p>"];
        yield 'unclosed heading stays text' => ['[h1]T', '<p>[h1]T</p>'];
        yield 'closing tag only stays text' => ['T[/h1]', '<p>T[/h1]</p>'];
        yield 'heading with an option stays text' => ['[h1=x]T[/h1]', '<p>[h1=x]T[/h1]</p>'];
        yield 'mismatched closing tag' => ['[h1]T[/h2]', '<p>[h1]T[/h2]</p>'];
        yield 'h7 is no tag' => ['[h7]T[/h7]', '<p>[h7]T[/h7]</p>'];
    }

    #[DataProvider('headlineProvider')]
    public function testHeadlinesAreRenderedWithTheOption(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->render($input, true));
    }

    public function testTheTagsStayTextByDefault(): void
    {
        $this->assertSame('<p>[h1]T[/h1]</p>', $this->render('[h1]T[/h1]', false));
        $this->assertSame('<p>[h2]Title[/h2]<br />' . "\n" . 'text</p>', $this->render("[h2]Title[/h2]\ntext", false));
    }

    public function testTheOptionIsOffInAnEmptyConfig(): void
    {
        $this->assertSame('<p>[h1]T[/h1]</p>', (new Engine())->toHtml('[h1]T[/h1]'));
    }

    public function testTheOptionDoesNotChangeMarkdownHeadings(): void
    {
        $this->assertSame('<h3>T</h3>', $this->engine(true)->toHtml('# T', InputMode::Markdown));
    }

    private function render(string $input, bool $parseHeadlines): string
    {
        return $this->engine($parseHeadlines)->toHtml($input);
    }

    private function engine(bool $parseHeadlines): Engine
    {
        $config = new Config();
        $config->mergeRecursive(['parse_headlines' => $parseHeadlines]);

        return new Engine($config);
    }
}
