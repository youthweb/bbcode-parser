<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\RegexHelper;
use League\CommonMark\Util\Xml;
use Youthweb\BBCodeParser\Extension\Node\Username;

/**
 * Renders a mention as a link to the profile. An unsafe url leaves the mention as text.
 *
 * @internal
 */
final class UsernameRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement|string
    {
        if (! $node instanceof Username) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $mention = Xml::escape('@' . $node->getName());

        if (RegexHelper::isLinkPotentiallyUnsafe($node->getUrl())) {
            return $mention;
        }

        return new HtmlElement('a', ['href' => $node->getUrl()], $mention);
    }
}
