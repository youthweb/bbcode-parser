<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Youthweb\BBCodeParser\Extension\Node\TrustedHtml;

/**
 * Renders the HTML of the node as it is.
 *
 * @experimental
 * @internal
 */
final class TrustedHtmlRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof TrustedHtml) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        return $node->getLiteral();
    }
}
