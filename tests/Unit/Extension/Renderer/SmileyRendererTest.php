<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Unit\Extension\Renderer;

use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Extension\Node\Smiley;
use Youthweb\BBCodeParser\Extension\Renderer\SmileyRenderer;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\NoChildrenRenderer;

#[CoversClass(SmileyRenderer::class)]
#[CoversClass(Smiley::class)]
final class SmileyRendererTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function smileyProvider(): iterable
    {
        yield 'smiley' => [':-)', 'https://example.org/smile.gif', '<img src="https://example.org/smile.gif" alt=":-)" title=":-)" />'];
        yield 'the code is escaped' => [':-"<', 'https://example.org/a.gif', '<img src="https://example.org/a.gif" alt=":-&quot;&lt;" title=":-&quot;&lt;" />'];
        yield 'the url is escaped' => [':-)', 'https://example.org/a.gif?a=1&b="2"', '<img src="https://example.org/a.gif?a=1&amp;b=&quot;2&quot;" alt=":-)" title=":-)" />'];
        yield 'an unsafe url leaves the code as text' => [':-)', 'javascript:alert(1)', ':-)'];
        yield 'an unsafe url leaves the code as escaped text' => ['<3', 'javascript:alert(1)', '&lt;3'];
    }

    #[DataProvider('smileyProvider')]
    public function testRender(string $code, string $url, string $expected): void
    {
        $this->assertSame($expected, (string) (new SmileyRenderer())->render(new Smiley($code, $url), new NoChildrenRenderer()));
    }

    public function testItRejectsAnotherNode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new SmileyRenderer())->render(new Text('a'), new NoChildrenRenderer());
    }
}
