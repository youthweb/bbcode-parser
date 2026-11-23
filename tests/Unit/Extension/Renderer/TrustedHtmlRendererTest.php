<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Unit\Extension\Renderer;

use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Extension\Node\TrustedHtml;
use Youthweb\BBCodeParser\Extension\Renderer\TrustedHtmlRenderer;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\NoChildrenRenderer;

#[CoversClass(TrustedHtmlRenderer::class)]
#[CoversClass(TrustedHtml::class)]
final class TrustedHtmlRendererTest extends TestCase
{
    public function testTheHtmlIsRenderedAsItIs(): void
    {
        $html = '<img class="emoji" alt="😀" src="https://example.org/1f600.png">';

        $this->assertSame($html, (new TrustedHtmlRenderer())->render(new TrustedHtml($html), new NoChildrenRenderer()));
    }

    public function testItRejectsAnotherNode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TrustedHtmlRenderer())->render(new Text('a'), new NoChildrenRenderer());
    }
}
