<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Youthweb\BBCodeParser\Extension\Node\Underline;

/**
 * @experimental
 * @internal
 */
final class UnderlineRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        Underline::assertInstanceOf($node);

        return new HtmlElement('u', [], $childRenderer->renderNodes($node->children()));
    }
}
