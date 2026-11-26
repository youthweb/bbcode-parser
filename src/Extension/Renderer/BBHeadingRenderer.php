<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Youthweb\BBCodeParser\Extension\Node\BBHeading;

/**
 * Renders the heading of `[h1]` to `[h6]` as `<h1>` to `<h6>`.
 *
 * @internal
 */
final class BBHeadingRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        if (! $node instanceof BBHeading) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        return new HtmlElement('h' . min(6, max(1, $node->getLevel())), [], $childRenderer->renderNodes($node->children()));
    }
}
