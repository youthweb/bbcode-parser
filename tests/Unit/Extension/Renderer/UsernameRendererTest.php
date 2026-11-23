<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Unit\Extension\Renderer;

use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Extension\Node\Username;
use Youthweb\BBCodeParser\Extension\Renderer\UsernameRenderer;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\NoChildrenRenderer;

#[CoversClass(UsernameRenderer::class)]
#[CoversClass(Username::class)]
final class UsernameRendererTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function usernameProvider(): iterable
    {
        yield 'username' => ['alice', 'https://example.org/users/1', '<a href="https://example.org/users/1">@alice</a>'];
        yield 'the name is escaped' => ['a<b>', 'https://example.org/users/1', '<a href="https://example.org/users/1">@a&lt;b&gt;</a>'];
        yield 'the url is escaped' => ['alice', 'https://example.org/?a=1&b="2"', '<a href="https://example.org/?a=1&amp;b=&quot;2&quot;">@alice</a>'];
        yield 'an unsafe url leaves the mention as text' => ['alice', 'javascript:alert(1)', '@alice'];
    }

    #[DataProvider('usernameProvider')]
    public function testRender(string $name, string $url, string $expected): void
    {
        $this->assertSame($expected, (string) (new UsernameRenderer())->render(new Username($name, $url), new NoChildrenRenderer()));
    }

    public function testItRejectsAnotherNode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new UsernameRenderer())->render(new Text('a'), new NoChildrenRenderer());
    }
}
