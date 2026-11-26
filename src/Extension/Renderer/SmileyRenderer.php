<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\RegexHelper;
use League\CommonMark\Util\Xml;
use Youthweb\BBCodeParser\Extension\Node\Smiley;

/**
 * Renders a smiley as an image with the code as alt text and title. An unsafe url leaves the code as text.
 *
 * @internal
 */
final class SmileyRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement|string
    {
        if (! $node instanceof Smiley) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        if (RegexHelper::isLinkPotentiallyUnsafe($node->getUrl())) {
            return Xml::escape($node->getCode());
        }

        return new HtmlElement('img', [
            'src' => $node->getUrl(),
            'alt' => $node->getCode(),
            'title' => $node->getCode(),
        ], '', true);
    }
}
