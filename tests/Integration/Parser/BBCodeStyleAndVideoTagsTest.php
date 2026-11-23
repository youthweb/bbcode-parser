<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Parser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\BBCodeExtension;
use Youthweb\BBCodeParser\Extension\Node\Color;
use Youthweb\BBCodeParser\Extension\Node\Size;
use Youthweb\BBCodeParser\Extension\Node\Video;
use Youthweb\BBCodeParser\Extension\Renderer\ColorRenderer;
use Youthweb\BBCodeParser\Extension\Renderer\SizeRenderer;
use Youthweb\BBCodeParser\Extension\Renderer\VideoRenderer;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Parser\BBCodeTreeParser;
use Youthweb\BBCodeParser\Parser\SpikeHtmlRenderer;

/**
 * Back-to-back test of the color, size and video tags (#51): 1.x engine against the league node tree.
 */
#[CoversClass(BBCodeTreeParser::class)]
#[CoversClass(SpikeHtmlRenderer::class)]
#[CoversClass(BBCodeExtension::class)]
#[CoversClass(Color::class)]
#[CoversClass(Size::class)]
#[CoversClass(Video::class)]
#[CoversClass(ColorRenderer::class)]
#[CoversClass(SizeRenderer::class)]
#[CoversClass(VideoRenderer::class)]
final class BBCodeStyleAndVideoTagsTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function identicalProvider(): iterable
    {
        yield 'color' => ['[color=red]x[/color]'];
        yield 'color, uppercase tag' => ['[COLOR=red]x[/COLOR]'];
        yield 'color, mixed case name' => ['[color=gReEn]x[/color]'];
        yield 'color, every allowed name' => ['[color=Red]a[/color][color=Green]b[/color][color=Blue]c[/color][color=Yellow]d[/color][color=Black]e[/color][color=White]f[/color][color=Orange]g[/color][color=Grey]h[/color]'];
        yield 'color, hex' => ['[color=#12aB9f]x[/color]'];
        yield 'color, hex with three digits' => ['[color=#abc]x[/color]'];
        yield 'color, hex without hash' => ['[color=12ab9f]x[/color]'];
        yield 'color, hex too long' => ['[color=#12ab9fa]x[/color]'];
        yield 'color, unknown name' => ['[color=pink]x[/color]'];
        yield 'color, quoted option' => ['[color="blue"]x[/color]'];
        yield 'color without option' => ['[color]x[/color]'];
        yield 'color with empty option' => ['[color=]x[/color]'];
        yield 'color with style injection' => ['[color=red;background:url(x)]x[/color]'];
        yield 'color with markup in option' => ['[color="><script>"]x[/color]'];
        yield 'color with nested tag' => ['[color=blue][b]x[/b][/color]'];
        yield 'color nested in tag' => ['[b][color=blue]x[/color][/b]'];
        yield 'color in color' => ['[color=red]a[color=blue]b[/color]c[/color]'];
        yield 'color with special characters' => ['[color=red]<b>"x"[/color]'];
        yield 'color unclosed' => ['[color=red]x'];
        yield 'color, stray closing tag' => ['x[/color]'];
        yield 'color over a soft break' => ["[color=red]a\nb[/color]"];
        yield 'color closed by an outer tag' => ['[b][color=red]x[/b]y'];
        yield 'size' => ['[size=120]x[/size]'];
        yield 'size, uppercase tag' => ['[SIZE=120]x[/SIZE]'];
        yield 'size, lower bound' => ['[size=75]x[/size]'];
        yield 'size, upper bound' => ['[size=150]x[/size]'];
        yield 'size, below the bound' => ['[size=10]x[/size]'];
        yield 'size, above the bound' => ['[size=500]x[/size]'];
        yield 'size, zero' => ['[size=0]x[/size]'];
        yield 'size, negative' => ['[size=-20]x[/size]'];
        yield 'size, not a number' => ['[size=abc]x[/size]'];
        yield 'size, number with unit' => ['[size=120%]x[/size]'];
        yield 'size, number and text' => ['[size=120abc]x[/size]'];
        yield 'size, decimal' => ['[size=99.9]x[/size]'];
        yield 'size, quoted option' => ['[size="110"]x[/size]'];
        yield 'size without option' => ['[size]x[/size]'];
        yield 'size with empty option' => ['[size=]x[/size]'];
        yield 'size with nested tag' => ['[size=120][b]x[/b][/size]'];
        yield 'size in color' => ['[color=red][size=120]x[/size][/color]'];
        yield 'size in size' => ['[size=120]a[size=80]b[/size]c[/size]'];
        yield 'size unclosed' => ['[size=120]x'];
        yield 'size, stray closing tag' => ['x[/size]'];
        yield 'video' => ['[video]http://www.youtube.com/watch?v=x_O0YiUFtxc[/video]'];
        yield 'v' => ['[v]http://vimeo.com/11902475[/v]'];
        yield 'v, short youtube url' => ['[v]https://youtu.be/x_O0YiUFtxc[/v]'];
        yield 'youtube' => ['[youtube]http://www.youtube.com/watch?v=x_O0YiUFtxc[/youtube]'];
        yield 'video, uppercase tag' => ['[VIDEO]http://a.de/v[/VIDEO]'];
        yield 'video without scheme' => ['[v]a.de/v[/v]'];
        yield 'video, not a url' => ['[v]not a url[/v]'];
        yield 'video unclosed' => ['[v]http://a.de/v'];
        yield 'video with option stays text' => ['[v=x]http://a.de/v[/v]'];
        yield 'video content is raw' => ['[v]http://a.de/[b]x[/b][/v]'];
        yield 'video in url' => ['[url=http://b.de][v]http://a.de/v[/v][/url]'];
        yield 'video in bold' => ['[b][v]http://a.de/v[/v][/b]'];
        yield 'video with unsafe scheme' => ['[v]javascript:alert(1)[/v]'];
        yield 'video, stray closing tag' => ['x[/v]'];
    }

    #[DataProvider('identicalProvider')]
    public function testIdenticalToOldEngine(string $input): void
    {
        $this->assertSame($this->old($input), $this->new($input));
    }

    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function identicalWithConfigProvider(): iterable
    {
        yield 'color, own allowed colors' => ['[color=pink]x[/color]', ['callbacks' => ['color_param' => ['allowed_colors' => ['Pink']]]]];
        yield 'color, own default color' => ['[color=nope]x[/color]', ['callbacks' => ['color_param' => ['default_color' => 'Blue']]]];
        yield 'size, own bounds' => ['[size=10]a[/size][size=999]b[/size]', ['callbacks' => ['size_param' => ['min_size' => 50, 'max_size' => 200]]]];
        yield 'video, embed_videos off' => ['[v]http://a.de/v[/v]', ['embed_videos' => false]];
    }

    /**
     * @param array<string, mixed> $config
     */
    #[DataProvider('identicalWithConfigProvider')]
    public function testIdenticalToOldEngineWithConfig(string $input, array $config): void
    {
        $this->assertSame($this->old($input, $config), $this->new($input, $config));
    }

    /**
     * @return iterable<string, array{string, string, string}> input, 1.x output, new output
     */
    public static function differenceProvider(): iterable
    {
        yield 'color without content, with option' => ['[color=red][/color]', '<p><span style="color:Red;"></span></p>', '<p></p>'];
        yield 'color, option with spaces' => ['[color= blue ]x[/color]', '<p><span style="color:Red;">x</span></p>', '<p><span style="color:Blue;">x</span></p>'];
        yield 'color with an ampersand entity' => ['[color=red]&amp;[/color]', '<p><span style="color:Red;">&amp;</span></p>', '<p><span style="color:Red;">&amp;amp;</span></p>'];
        yield 'color with a bare url' => [
            '[color=red]see http://a.de/x[/color]',
            '<p><span style="color:Red;">see &lt;a target=&quot;_blank&quot; href=&quot;http://a.de/x&quot;&gt;http://a.de/x&lt;/a&gt;</span></p>',
            '<p><span style="color:Red;">see <a target="_blank" href="http://a.de/x">http://a.de/x</a></span></p>',
        ];
        yield 'video with spaces' => ['[v]  http://a.de/v  [/v]', '<p>http://a.de/v</p>', '<p>  http://a.de/v  </p>'];
        yield 'video, url with a special character' => [
            '[v]http://a.de/?a=1&b=2[/v]',
            '<p><a target="_blank" href="http://a.de/?a=1&amp;b=2">http://a.de/?a=1&b=2</a></p>',
            '<p><a target="_blank" href="http://a.de/?a=1&amp;b=2">http://a.de/?a=1&amp;b=2</a></p>',
        ];
        yield 'video, url with a tag' => [
            '[v]http://a.de/<b>[/v]',
            '<p><a target="_blank" href="http://a.de/&lt;b&gt;">http://a.de/<b></a></p>',
            '<p><a target="_blank" href="http://a.de/&lt;b&gt;">http://a.de/&lt;b&gt;</a></p>',
        ];
        yield 'video, no url but a tag' => ['[v]<b>x</b>[/v]', '<p><b>x</b></p>', '<p>&lt;b&gt;x&lt;/b&gt;</p>'];
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
        yield 'color in a list item' => ['[list][*][color=red]x[/color][/list]', "<ul>\n\t<li><span style=\"color:Red;\">x</span></li>\n</ul>"];
        yield 'size in a quote' => ['[quote][size=120]x[/size][/quote]', "<blockquote title=\"Zitat\">\n<p><span style=\"font-size:120%;\">x</span></p>\n</blockquote>"];
        yield 'color is closed at a quote' => ['[color=red]a[quote]b[/quote]c[/color]', "<p><span style=\"color:Red;\">a</span></p>\n<blockquote title=\"Zitat\">\n<p>b</p>\n</blockquote>\n<p>c[/color]</p>"];
        yield 'video without content' => ['[v][/v]', '<p></p>'];
    }

    #[DataProvider('newEngineProvider')]
    public function testNewEngine(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->new($input));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function old(string $input, array $config = []): string
    {
        return (new Manager())->parse($input, $config);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function new(string $input, array $config = []): string
    {
        $object = new Config();
        $object->mergeRecursive($config);

        $html = (new SpikeHtmlRenderer($object))->render((new BBCodeTreeParser())->parse($input));

        // The markup of the inline tags is covered by BBCodeTreeParserTest
        return strtr($html, ['<strong>' => '<b>', '</strong>' => '</b>']);
    }
}
