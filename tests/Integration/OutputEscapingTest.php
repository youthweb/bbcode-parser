<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Manager;

/**
 * The options and the content of tags must not reach the output as markup or script.
 */
#[CoversClass(Manager::class)]
final class OutputEscapingTest extends TestCase
{
    #[DataProvider('quoteProvider')]
    public function testOptionOfAQuoteIsEscaped(string $text, string $expected): void
    {
        $this->assertSame($expected, (new Manager())->parse($text));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function quoteProvider(): iterable
    {
        return [
            'quote' => [
                '[quote=<svg/onload=alert(1)>]x[/quote]',
                '<p><blockquote title="Zitat"><cite>&lt;svg/onload=alert(1)&gt;</cite>x</blockquote></p>',
            ],
            'q' => [
                '[q=<svg/onload=alert(1)>]x[/q]',
                '<p><blockquote title="Zitat"><cite>&lt;svg/onload=alert(1)&gt;</cite>x</blockquote></p>',
            ],
            'Z' => [
                '[Z=<svg/onload=alert(1)>]x[/Z]',
                '<p><blockquote title="Zitat"><cite>&lt;svg/onload=alert(1)&gt;</cite>x</blockquote></p>',
            ],
            'quoted option with quotes' => [
                '[quote="a&quot;b"]x[/quote]',
                '<p><blockquote title="Zitat"><cite>a&quot;b</cite>x</blockquote></p>',
            ],
            'ampersand' => [
                '[quote="Tom & Jerry"]x[/quote]',
                '<p><blockquote title="Zitat"><cite>Tom &amp; Jerry</cite>x</blockquote></p>',
            ],
            'plain name stays' => [
                '[quote="Albert Einstein"]x[/quote]',
                '<p><blockquote title="Zitat"><cite>Albert Einstein</cite>x</blockquote></p>',
            ],
        ];
    }

    #[DataProvider('videoProvider')]
    public function testContentOfAVideoIsEscaped(string $text, string $expected): void
    {
        $this->assertSame($expected, (new Manager())->parse($text));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function videoProvider(): iterable
    {
        return [
            'v, no url' => [
                '[v]<svg/onload=alert(1)>[/v]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'video, no url' => [
                '[video]<svg/onload=alert(1)>[/video]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'youtube, no url' => [
                '[youtube]<svg/onload=alert(1)>[/youtube]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'url with markup' => [
                '[v]http://example.org/<svg/onload=alert(1)>[/v]',
                '<p><a target="_blank" href="http://example.org/&lt;svg/onload=alert(1)&gt;">http://example.org/&lt;svg/onload=alert(1)&gt;</a></p>',
            ],
            'plain url is linked' => [
                '[v]http://example.org/video[/v]',
                '<p><a target="_blank" href="http://example.org/video">http://example.org/video</a></p>',
            ],
            'url with ampersand' => [
                '[v]http://example.org/video?a=1&b=2[/v]',
                '<p><a target="_blank" href="http://example.org/video?a=1&amp;b=2">http://example.org/video?a=1&b=2</a></p>',
            ],
        ];
    }

    #[DataProvider('imageProvider')]
    public function testContentOfAnImageIsEscaped(string $text, string $expected): void
    {
        $html = (new Manager())->parse($text, ['callbacks' => ['image' => ['force_check' => false]]]);

        $this->assertSame($expected, $html);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function imageProvider(): iterable
    {
        return [
            'img, no url' => [
                '[img]<svg/onload=alert(1)>[/img]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'pic, no url' => [
                '[pic]<svg/onload=alert(1)>[/pic]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'img, quote in the url' => [
                '[img]http://example.org/x.png" onerror="alert(1)[/img]',
                '<p>http://example.org/x.png&quot; onerror=&quot;alert(1)</p>',
            ],
            'img in a url tag, no url' => [
                '[url=http://example.org][img]<svg/onload=alert(1)>[/img][/url]',
                '<p><a target="_blank" href="http://example.org">&lt;svg/onload=alert(1)&gt;</a></p>',
            ],
            'plain image stays' => [
                '[img]http://example.org/x.png[/img]',
                '<p><a target="_blank" href="http://example.org/x.png"><img class="img-responsive" border="0" src="http://example.org/x.png" alt="x" /></a></p>',
            ],
        ];
    }

    #[DataProvider('emailProvider')]
    public function testTextOfAProtectedEmailStaysInsideTheScriptString(string $text, string $contained, ?string $notContained): void
    {
        $html = (new Manager())->parse($text, ['callbacks' => ['email_content' => ['protect_email' => true]]]);

        $this->assertStringContainsString($contained, $html);

        if ($notContained !== null) {
            $this->assertStringNotContainsString($notContained, $html);
        }

        // Only the own script element is in the output, and it is closed once.
        $this->assertSame(1, substr_count(strtolower($html), '<script'));
        $this->assertSame(1, substr_count(strtolower($html), '</script>'));
    }

    /**
     * @return iterable<string, array{string, string, string|null}>
     */
    public static function emailProvider(): iterable
    {
        return [
            'quote ends the string' => [
                "[email=a@b.de]x');alert(1);//[/email]",
                "x\\u0027);alert(1);//</a>",
                "x');alert(1)",
            ],
            'script end tag' => [
                '[email=a@b.de]</script><b>x[/email]',
                "&lt;/script&gt;&lt;b&gt;x</a>",
                null,
            ],
            'backslash' => [
                '[email=a@b.de]x\\[/email]',
                "x\\\\</a>",
                null,
            ],
            'plain text stays' => [
                '[email=a@b.de]Mail me[/email]',
                '>Mail me</a>',
                null,
            ],
        ];
    }
}
