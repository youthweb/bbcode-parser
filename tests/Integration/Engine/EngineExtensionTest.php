<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Engine;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Engine;
use Youthweb\BBCodeParser\InputMode;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\ImageFilterExtension;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\SmileyExtension;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\UsernameExtension;

/**
 * Extension points of the league engine (#53): a project adds league extensions to the engine.
 */
#[CoversClass(Engine::class)]
final class EngineExtensionTest extends TestCase
{
    /**
     * @return iterable<string, array{InputMode, string, string}>
     */
    public static function imageFilterProvider(): iterable
    {
        $img = '<img class="img-responsive" src="https://files.example.org/42.png" />';
        $linked = '<a href="https://files.example.org/42/full.png" target="_blank">' . $img . '</a>';

        yield 'BBCode: an image id is filtered' => [InputMode::BBCode, '[img]42[/img]', '<p>' . $linked . '</p>'];
        yield 'BBCode: in a link it is not linked again' => [InputMode::BBCode, '[url=https://example.org][img]42[/img][/url]', '<p><a target="_blank" href="https://example.org">' . $img . '</a></p>'];
        yield 'BBCode: an url is left to the default renderer' => [InputMode::BBCode, '[img]https://example.org/a.png[/img]', '<p><a target="_blank" href="https://example.org/a.png"><img class="img-responsive" border="0" src="https://example.org/a.png" alt="a" /></a></p>'];
        yield 'Markdown: an image id is filtered' => [InputMode::Markdown, '![](42)', '<p>' . $linked . '</p>'];
        yield 'Markdown: an url is left to the default renderer' => [InputMode::Markdown, '![](https://example.org/a.png)', '<p><a target="_blank" href="https://example.org/a.png"><img class="img-responsive" border="0" src="https://example.org/a.png" alt="a" /></a></p>'];
    }

    #[DataProvider('imageFilterProvider')]
    public function testARendererWithAHigherPriorityFiltersTheImage(InputMode $mode, string $input, string $expected): void
    {
        $engine = new Engine($this->config(), [new ImageFilterExtension()]);

        $this->assertSame($expected, $engine->toHtml($input, $mode));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function textReplacementProvider(): iterable
    {
        $smile = '<img src="https://example.org/smilies/smile.gif" alt=":-)" title=":-)" />';
        $user = '<a href="https://example.org/users/1">@alice</a>';

        yield 'smiley' => ['a :-) b', '<p>a ' . $smile . ' b</p>'];
        yield 'two smilies' => ['a :-);-)', '<p>a ' . $smile . '<img src="https://example.org/smilies/wink.gif" alt=";-)" title=";-)" /></p>'];
        yield 'emoji as trusted html' => ['a 😀', '<p>a <img class="emoji" alt="😀" src="https://example.org/emoji/1f600.png"></p>'];
        yield 'username' => ['hi @alice!', '<p>hi ' . $user . '!</p>'];
        yield 'username at the start' => ['@alice', '<p>' . $user . '</p>'];
        yield 'unknown username stays text' => ['hi @nobody', '<p>hi @nobody</p>'];
        yield 'no username in an address' => ['a@alice.de', '<p>a@alice.de</p>'];
        yield 'both' => ['@alice :-)', '<p>' . $user . ' ' . $smile . '</p>'];
        yield 'in a tag' => ['[b]@alice :-)[/b]', '<p><strong>' . $user . ' ' . $smile . '</strong></p>'];
        yield 'in a quote' => ['[quote]:-)[/quote]', "<blockquote title=\"Zitat\">\n<p>" . $smile . "</p>\n</blockquote>"];
        yield 'in a list' => ['[list][*]@alice[/list]', "<ul>\n\t<li>" . $user . '</li>' . "\n</ul>"];
        yield 'not in a code block' => ['[code]@alice :-)[/code]', '<pre><code>@alice :-)</code></pre>'];
        yield 'not in noparse' => ['[noparse]@alice :-)[/noparse]', '<p>@alice :-)</p>'];
        yield 'not in a link' => ['[url=https://example.org]@alice :-)[/url]', '<p><a target="_blank" href="https://example.org">@alice :-)</a></p>'];
        yield 'not in an email' => ['[email=a@example.org]@alice :-)[/email]', '<p><a href="mailto:a@example.org">@alice :-)</a></p>'];
        yield 'not in an autolinked url' => ['https://example.org/@alice :-)', '<p><a target="_blank" href="https://example.org/@alice">https://example.org/@alice</a> ' . $smile . '</p>'];
        yield 'text next to a link' => ['@alice [url=https://example.org]x[/url] :-)', '<p>' . $user . ' <a target="_blank" href="https://example.org">x</a> ' . $smile . '</p>'];
    }

    #[DataProvider('textReplacementProvider')]
    public function testTextReplacementInBBCode(string $input, string $expected): void
    {
        $engine = new Engine($this->config(), [new UsernameExtension(), new SmileyExtension()]);

        $this->assertSame($expected, $engine->toHtml($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function markdownTextReplacementProvider(): iterable
    {
        $smile = '<img src="https://example.org/smilies/smile.gif" alt=":-)" title=":-)" />';
        $user = '<a href="https://example.org/users/1">@alice</a>';

        yield 'smiley and username' => ['@alice :-)', '<p>' . $user . ' ' . $smile . '</p>'];
        yield 'in strong' => ['**@alice**', '<p><strong>' . $user . '</strong></p>'];
        yield 'in a heading' => ['# @alice', '<h3>' . $user . '</h3>'];
        yield 'in a list' => ['- :-)', "<ul>\n\t<li>" . $smile . "</li>\n</ul>"];
        yield 'not in inline code' => ['`@alice :-)`', '<p><code>@alice :-)</code></p>'];
        yield 'not in a code block' => ["```\n@alice :-)\n```", '<pre><code>@alice :-)</code></pre>'];
        yield 'not in a link' => ['[@alice :-)](https://example.org)', '<p><a target="_blank" href="https://example.org">@alice :-)</a></p>'];
        yield 'not in an autolinked url' => ['<https://example.org/@alice>', '<p><a target="_blank" href="https://example.org/@alice">https://example.org/@alice</a></p>'];
    }

    #[DataProvider('markdownTextReplacementProvider')]
    public function testTextReplacementInMarkdown(string $input, string $expected): void
    {
        $engine = new Engine($this->config(), [new UsernameExtension(), new SmileyExtension()]);

        $this->assertSame($expected, $engine->toHtml($input, InputMode::Markdown));
    }

    public function testAListenerWithTheDefaultPriorityRunsAfterTheAutolinker(): void
    {
        // The smiley is in the text of the url: the url is a link already and is left alone.
        $engine = new Engine($this->config(), [new SmileyExtension()]);

        $this->assertSame('<p><a target="_blank" href="https://example.org/a:-)b">https://example.org/a:-)b</a></p>', $engine->toHtml('https://example.org/a:-)b'));
    }

    public function testAListenerWithAHigherPriorityRunsBeforeTheAutolinker(): void
    {
        $engine = new Engine($this->config(), [new SmileyExtension(200)]);

        // Before the autolinker the url is plain text, so the smiley is replaced inside of it.
        $this->assertStringContainsString('<img src="https://example.org/smilies/smile.gif"', $engine->toHtml('https://example.org/a:-)b'));
    }

    public function testTheMarkdownTreeIsBuiltBeforeTheListenersOfAnExtension(): void
    {
        $engine = new Engine($this->config(), [new UsernameExtension()]);

        // A mailto link becomes an email node and a fenced code becomes a code block, both are skipped.
        $this->assertSame('<pre><code>@alice</code></pre>', $engine->toHtml("```\n@alice\n```", InputMode::Markdown));
    }

    public function testWithoutAnExtensionTheDefaultRendererIsUsed(): void
    {
        $html = (new Engine($this->config()))->toHtml('[img]42[/img]');

        $this->assertStringContainsString('src="http://42"', $html);
        $this->assertStringNotContainsString('files.example.org', $html);
    }

    public function testExtensionsMayBeAnyIterable(): void
    {
        $generator = (static function () {
            yield new ImageFilterExtension();
        })();

        $html = (new Engine($this->config(), $generator))->toHtml('[img]42[/img]');

        $this->assertStringContainsString('files.example.org/42.png', $html);
    }

    private function config(): Config
    {
        // The image check would ask the network.
        return (new Config())->mergeRecursive(['callbacks' => ['image' => ['force_check' => false]]]);
    }
}
