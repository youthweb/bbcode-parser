<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Parser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\AutolinkListener;
use Youthweb\BBCodeParser\Extension\BBCodeExtension;
use Youthweb\BBCodeParser\Extension\Node\Email;
use Youthweb\BBCodeParser\Extension\Node\Image;
use Youthweb\BBCodeParser\Extension\Renderer\EmailRenderer;
use Youthweb\BBCodeParser\Extension\Renderer\ImageRenderer;
use Youthweb\BBCodeParser\Extension\Renderer\LinkRenderer;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Tests\Fixtures\MockHttpStreamWrapper;
use Youthweb\BBCodeParser\Tests\Fixtures\ValidationMock;
use Youthweb\BBCodeParser\Parser\BBCodeTreeParser;
use Youthweb\BBCodeParser\Parser\SpikeHtmlRenderer;

/**
 * Back-to-back test of the link, email and image tags and of the autolinking (#49):
 * 1.x engine against the league node tree.
 */
#[CoversClass(BBCodeTreeParser::class)]
#[CoversClass(SpikeHtmlRenderer::class)]
#[CoversClass(BBCodeExtension::class)]
#[CoversClass(AutolinkListener::class)]
#[CoversClass(Email::class)]
#[CoversClass(Image::class)]
#[CoversClass(EmailRenderer::class)]
#[CoversClass(ImageRenderer::class)]
#[CoversClass(LinkRenderer::class)]
final class BBCodeLinkTagsTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function identicalProvider(): iterable
    {
        yield 'url' => ['[url]http://a.de[/url]'];
        yield 'url with option' => ['[url=http://a.de]x[/url]'];
        yield 'url with quoted option' => ['[url="http://a.de"]q[/url]'];
        yield 'url with nested tag' => ['[url=http://a.de][b]x[/b][/url]'];
        yield 'url without scheme' => ['[url=www.a.de]w[/url]'];
        yield 'url with trailing slash' => ['[url=http://a.de/]x[/url]'];
        yield 'url with empty option' => ['[url=]x[/url]'];
        yield 'url with tags and no url' => ['[url][b]x[/b][/url]'];
        yield 'url without scheme in content' => ['[url]www.a.de/x[/url]'];
        yield 'url, protocol relative' => ['[url=//a.de]p[/url]'];
        yield 'url, mailto is no scheme://' => ['[url=mailto:a@b.de]m[/url]'];
        yield 'url, other scheme' => ['[url=ftp://a.de/x]f[/url]'];
        yield 'url, not a url' => ['[url]not a url[/url]'];
        yield 'url, text is the url' => ['[url=http://a.de]http://a.de/b[/url]'];
        yield 'url, youthweb.net has no target' => ['[url=http://youthweb.net/x]y[/url]'];
        yield 'url unclosed' => ['[url=http://a.de]text'];
        yield 'url, uppercase' => ['[URL=http://a.de]x[/URL]'];
        yield 'ywlink' => ['[YWLINK=http://a.de]y[/YWLINK]'];
        yield 'ywlink without option stays text' => ['[YWLINK]http://a.de[/YWLINK]'];
        yield 'ywlink, protocol relative' => ['[YWLINK=//a.de]y[/YWLINK]'];
        yield 'ywlink with nested tag' => ['[YWLINK=http://a.de][b]y[/b][/YWLINK]'];
        yield 'ywlink unclosed' => ['[YWLINK=http://a.de]y'];
        yield 'email unclosed' => ['[email=a@b.de]text'];
        yield 'email with empty option' => ['[email=]a@b.de[/email]'];
        yield 'email' => ['[email]a@b.de[/email]'];
        yield 'email with option' => ['[email=a@b.de]mail[/email]'];
        yield 'email with option and nested tag' => ['[email=a@b.de][b]m[/b][/email]'];
        yield 'email, invalid option' => ['[email=x]m[/email]'];
        yield 'email, invalid content' => ['[email]no[/email]'];
        yield 'img' => ['[img]http://a.de/x.png[/img]'];
        yield 'pic' => ['[PIC]http://a.de/x.png[/PIC]'];
        yield 'img unclosed' => ['[img]open'];
        yield 'img with option stays text' => ['[img=x]http://a.de/x.png[/img]'];
        yield 'img with spaces' => ['[img]  http://a.de/x.png  [/img]'];
        yield 'img without scheme' => ['[img]a.de/x.png[/img]'];
        yield 'img, not a url' => ['[img]not a url[/img]'];
        yield 'img content is raw' => ['[img]http://a.de/[b]x[/b].png[/img]'];
        yield 'img in url has no link of its own' => ['[url=http://a.de][img]http://a.de/x.png[/img][/url]'];
        yield 'bare urls' => ['see www.a.de and http://a.de/x. ok'];
        yield 'bare url with tag characters' => ['http://a.de/x?a=<b>'];
        yield 'bare email is not linked' => ['mail a@b.de now'];
        yield 'bare url in code' => ['[code]http://a.de[/code]'];
        yield 'bare url in url tag' => ['[url=http://b.de]see http://a.de[/url]'];
        yield 'bare url over a soft break' => ["http://a.de\nhttp://b.de"];
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
        yield 'short url' => [
            'http://www.example.com/irgend/eine/lange/url.html',
            ['callbacks' => ['url_content' => ['short_url' => true, 'short_url_length' => 25]]],
        ];
        yield 'long short url' => [
            'http://www.example.com/irgend/eine/lange/url.html',
            ['callbacks' => ['url_content' => ['short_url' => true, 'short_url_length' => 40]]],
        ];
        yield 'short url in tag' => [
            '[url]http://www.example.com/irgend/eine/lange/url.html[/url]',
            ['callbacks' => ['url_content' => ['short_url' => true, 'short_url_length' => 25]]],
        ];
        yield 'no target' => [
            '[url=http://a.de]x[/url] http://b.de',
            ['callbacks' => ['url_content' => ['target' => null]]],
        ];
        yield 'protect email' => [
            '[email=a@b.de]m[/email] [email]c@d.de[/email]',
            ['callbacks' => ['email_content' => ['protect_email' => true]]],
        ];
        yield 'urls are not parsed' => [
            'see http://a.de [url=http://b.de]x[/url]',
            ['parse_urls' => false],
        ];
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
     * @return iterable<string, array{string, list<string>}>
     */
    public static function imageCheckProvider(): iterable
    {
        yield 'url of an image' => ['[img]http://example.org/a.jpg[/img]', ['Content-Type: image/jpeg']];
        yield 'pic, url of an image' => ['[PIC]http://example.org/a.jpg[/PIC]', ['Content-Type: image/jpeg']];
        yield 'pic, url of no image is only linked' => ['[PIC]http://example.org/a.html[/PIC]', ['Content-Type: text/html']];
        yield 'url of no image is only linked' => ['[img]http://example.org/a.html[/img]', ['Content-Type: text/html']];
        yield 'url of no image in a url tag is text' => ['[url=http://b.de][img]http://example.org/a.html[/img][/url]', ['Content-Type: text/html']];
        yield 'url of an image in a url tag' => ['[url=http://b.de][img]http://example.org/a.jpg[/img][/url]', ['Content-Type: image/jpeg']];
    }

    /**
     * The check sends a HEAD request, the wrapper answers it.
     *
     * @param list<string> $headers
     */
    #[DataProvider('imageCheckProvider')]
    public function testImageCheckLikeOldEngine(string $input, array $headers): void
    {
        stream_wrapper_unregister('http');
        stream_wrapper_register('http', MockHttpStreamWrapper::class);

        try {
            MockHttpStreamWrapper::$mockMetaData = $headers;
            MockHttpStreamWrapper::$mockResponseCode = 'HTTP/1.1 200 OK';

            ValidationMock::resetImageCounter();
            $old = (new Manager())->parse($input);

            ValidationMock::resetImageCounter();
            $new = (new SpikeHtmlRenderer())->render((new BBCodeTreeParser())->parse($input));
        } finally {
            stream_wrapper_restore('http');
            ValidationMock::resetImageCounter();
        }

        $this->assertSame($old, strtr($new, ['<strong>' => '<b>', '</strong>' => '</b>']));
    }

    /**
     * Documented differences to 1.x.
     *
     * @return iterable<string, array{string, string, string}>
     */
    public static function differenceProvider(): iterable
    {
        yield 'ywlink with javascript: is plain text, 1.x leaves the tag as text' => [
            '[YWLINK=javascript:alert(1)]x[/YWLINK]',
            '<p>[YWLINK=javascript:alert(1)]x[/YWLINK]</p>',
            '<p>x</p>',
        ];
        yield 'an option with a space is not a url, 1.x reads only up to the space' => [
            '[YWLINK=not a url]y[/YWLINK]',
            '<p><a target="_blank" href="http://not">y</a></p>',
            '<p>y</p>',
        ];
        yield 'javascript: is plain text, 1.x rewrites it to http://x' => [
            '[url=javascript:alert(1)]x[/url]',
            '<p><a target="_blank" href="http://x">x</a></p>',
            '<p>x</p>',
        ];
        yield 'javascript:// with a line break is plain text, 1.x rewrites it to http://x' => [
            '[url=javascript://%0aalert(1)]x[/url]',
            '<p><a target="_blank" href="http://x">x</a></p>',
            '<p>x</p>',
        ];
        yield 'ampersand in the text of a url is escaped, 1.x prints it raw' => [
            '[url]http://a.de/?a=1&b=2[/url]',
            '<p><a target="_blank" href="http://a.de/?a=1&amp;b=2">http://a.de/?a=1&b=2</a></p>',
            '<p><a target="_blank" href="http://a.de/?a=1&amp;b=2">http://a.de/?a=1&amp;b=2</a></p>',
        ];
        yield 'bare url with an ampersand, 1.x prints it raw' => [
            'bare http://a.de/x?a=1&b=2',
            '<p>bare <a target="_blank" href="http://a.de/x?a=1&amp;b=2">http://a.de/x?a=1&b=2</a></p>',
            '<p>bare <a target="_blank" href="http://a.de/x?a=1&amp;b=2">http://a.de/x?a=1&amp;b=2</a></p>',
        ];
        yield 'bare url in a tag is a link, 1.x renders escaped html' => [
            '[b]http://a.de[/b]',
            '<p><b>&lt;a target=&quot;_blank&quot; href=&quot;http://a.de&quot;&gt;http://a.de&lt;/a&gt;</b></p>',
            '<p><b><a target="_blank" href="http://a.de">http://a.de</a></b></p>',
        ];
        yield 'empty url renders an empty paragraph, 1.x renders nothing' => [
            '[url][/url]',
            '',
            '<p></p>',
        ];
        yield 'bare url in an email tag stays text, 1.x links it' => [
            '[email]http://a.de[/email]',
            '<p><a target="_blank" href="http://a.de">http://a.de</a></p>',
            '<p>http://a.de</p>',
        ];
    }

    /**
     * With protect_email the text lands in a javascript string. The parser writes quotes and backslashes as
     * escape sequences, the engine writes them as entities. Neither lets the text end the string.
     */
    public function testProtectedEmailTextCannotBreakOutOfTheScript(): void
    {
        $config = ['callbacks' => ['email_content' => ['protect_email' => true]]];
        $input = "[email=a@b.de]x');alert(1);//\\ l1\nl2[/email]";

        $old = $this->old($input, $config);
        $this->assertStringContainsString('x\\u0027);alert(1);//', $old);
        $this->assertStringNotContainsString("x');alert(1);//", $old);
        $this->assertSame(
            '<p><script type="text/javascript">(function() {var user = "a";var at = "@";var server = "b.de";'
            . "document.write('<a href=\"' + 'mail' + 'to:' + user + at + server + '\">x&#39;);alert(1);//&#92; l1\\u003Cbr /\\u003E&#10;l2</a>');})();</script></p>",
            $this->new($input, $config),
        );
    }

    #[DataProvider('differenceProvider')]
    public function testDocumentedDifferences(string $input, string $old, string $new): void
    {
        $this->assertSame($old, $this->old($input));
        $this->assertSame($new, $this->new($input));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function old(string $input, array $config = []): string
    {
        $config['callbacks']['image']['force_check'] ??= false;

        return (new Manager())->parse($input, $config);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function new(string $input, array $config = []): string
    {
        $config['callbacks']['image']['force_check'] ??= false;

        $object = new Config();
        $object->mergeRecursive($config);

        $html = (new SpikeHtmlRenderer($object))->render((new BBCodeTreeParser())->parse($input));

        // The markup of the inline tags is covered by BBCodeTreeParserTest
        return strtr($html, ['<strong>' => '<b>', '</strong>' => '</b>']);
    }
}
