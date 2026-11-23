<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Youthweb\BBCodeParser\Config;

/**
 * Renders a heading, moved down by the config `heading_offset`: the level stops at 6.
 *
 * @experimental
 * @internal
 */
final readonly class HeadingRenderer implements NodeRendererInterface
{
    public function __construct(private Config $config) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        if (! $node instanceof Heading) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $offset = max(0, (int) $this->config->get('heading_offset', 2));
        $level = min(6, $node->getLevel() + $offset);

        return new HtmlElement('h' . $level, [], $childRenderer->renderNodes($node->children()));
    }
}
