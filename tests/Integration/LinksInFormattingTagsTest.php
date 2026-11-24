<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\HtmlTextNode;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Visitor\VisitorCollection;
use Youthweb\BBCodeParser\Visitor\VisitorSmiley;
use Youthweb\BBCodeParser\Visitor\VisitorUrl;

/**
 * A link that a visitor makes from a bare url is markup, the text of the user around it is still escaped.
 */
#[CoversClass(Manager::class)]
#[CoversClass(VisitorUrl::class)]
#[CoversClass(HtmlTextNode::class)]
final class LinksInFormattingTagsTest extends TestCase
{
    private const LINK = '<a target="_blank" href="http://example.org">http://example.org</a>';

    #[DataProvider('formattingProvider')]
    public function testBareUrlInAFormattingTagIsALink(string $text, string $expected): void
    {
        $this->assertSame($expected, (new Manager())->parse($text));
    }

    public static function formattingProvider(): \Iterator
    {
        yield 'b' => ['[b]example.org[/b]', '<p><b>' . self::LINK . '</b></p>'];
        yield 'i' => ['[i]example.org[/i]', '<p><i>' . self::LINK . '</i></p>'];
        yield 'u' => ['[u]example.org[/u]', '<p><u>' . self::LINK . '</u></p>'];
        yield 'size' => ['[size=12]example.org[/size]', '<p><span style="font-size:75%;">' . self::LINK . '</span></p>'];
        yield 'color' => ['[color=red]example.org[/color]', '<p><span style="color:Red;">' . self::LINK . '</span></p>'];
        yield 'with a scheme' => ['[i]http://example.org[/i]', '<p><i>' . self::LINK . '</i></p>'];
        yield 'nested' => ['[b][i]example.org[/i][/b]', '<p><b><i>' . self::LINK . '</i></b></p>'];
        yield 'text around the link' => ['[b]see example.org now[/b]', '<p><b>see ' . self::LINK . ' now</b></p>'];
        yield 'two links and a tag between them' => [
            '[b]example.org and [i]x[/i] example.org[/b]',
            '<p><b>' . self::LINK . ' and <i>x</i> ' . self::LINK . '</b></p>',
        ];
    }

    #[DataProvider('escapedTextProvider')]
    public function testTextOfTheUserStaysEscaped(string $text, string $expected): void
    {
        $this->assertSame($expected, (new Manager())->parse($text));
    }

    public static function escapedTextProvider(): \Iterator
    {
        yield 'tag in a bold text' => ['[b]a <i> b[/b]', '<p><b>a &lt;i&gt; b</b></p>'];
        yield 'tag in an italic text' => ['[i]a <i> b[/i]', '<p><i>a &lt;i&gt; b</i></p>'];
        yield 'tag in a size text' => ['[size=12]a <i> b[/size]', '<p><span style="font-size:75%;">a &lt;i&gt; b</span></p>'];
        yield 'tag in a color text' => ['[color=red]a <i> b[/color]', '<p><span style="color:Red;">a &lt;i&gt; b</span></p>'];
        yield 'tag next to a link' => ['[b]<i>example.org</i>[/b]', '<p><b>&lt;i&gt;' . self::LINK . '&lt;/i&gt;</b></p>'];
        yield 'script next to a link' => ['[u]example.org <script>alert(1)</script>[/u]', '<p><u>' . self::LINK . ' &lt;script&gt;alert(1)&lt;/script&gt;</u></p>'];
    }

    #[DataProvider('noLinkProvider')]
    public function testBareUrlWhereTheContentIsNotParsedIsNoLink(string $text, string $expected): void
    {
        $this->assertSame($expected, (new Manager())->parse($text));
    }

    public static function noLinkProvider(): \Iterator
    {
        yield 'code' => ['[code]example.org[/code]', '<pre><code>example.org</code></pre>'];
        yield 'url, one link only' => ['[url]example.org[/url]', '<p><a target="_blank" href="http://example.org">example.org</a></p>'];
        yield 'noparse' => ['[noparse]example.org[/noparse]', '<p>example.org</p>'];
    }

    public function testSmileyAndLinkInOneTag(): void
    {
        $manager = new Manager();
        $config = ['parse_smilies' => true, 'visitor' => ['smiley' => new VisitorSmiley()]];

        $this->assertSame(
            '<p><b><img src="https://youthweb.net/vendor/smilies/smile0001.gif" alt=":-)" title=":-)" /> ' . self::LINK . '</b></p>',
            $manager->parse('[b]:-) example.org[/b]', $config),
        );
    }

    public function testMarkupOfACustomVisitorInATagStaysEscaped(): void
    {
        $visitor = new class implements \Youthweb\BBCodeParser\Visitor\VisitorInterface {
            public function setConfig(\Youthweb\BBCodeParser\Config $config)
            {
                return $this;
            }

            public function visitDocumentElement(\JBBCode\DocumentElement $document_element)
            {
                foreach ($document_element->getChildren() as $child) {
                    $child->accept($this);
                }
            }

            public function visitTextNode(\JBBCode\TextNode $text_node)
            {
                $text_node->setValue('<em>x</em>');
            }

            public function visitElementNode(\JBBCode\ElementNode $element_node)
            {
                foreach ($element_node->getChildren() as $child) {
                    $child->accept($this);
                }
            }
        };
        $collection = new VisitorCollection();
        $collection->addVisitor($visitor);

        $this->assertSame('<p><b>&lt;em&gt;x&lt;/em&gt;</b></p>', (new Manager($collection))->parse('[b]a[/b]'));
    }
}
