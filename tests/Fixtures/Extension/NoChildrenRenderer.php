<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures\Extension;

use League\CommonMark\Renderer\ChildNodeRendererInterface;

/**
 * A child renderer for the renderers of nodes without children.
 */
final class NoChildrenRenderer implements ChildNodeRendererInterface
{
    public function renderNodes(iterable $nodes): string
    {
        throw new \LogicException('The node has no children.');
    }

    public function getBlockSeparator(): string
    {
        return "\n";
    }

    public function getInnerSeparator(): string
    {
        return "\n";
    }
}
