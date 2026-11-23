<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\Xml;

/**
 * Renders a quote with its cite as first element. The cite is the option of the tag and is always escaped.
 *
 * @experimental
 * @internal
 */
final class QuoteRenderer implements NodeRendererInterface
{
    /** Key of the cite in the data of the node */
    public const CITE = 'cite';

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        if (! $node instanceof BlockQuote) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $parts = [];

        $cite = $node->data->has(self::CITE) ? $node->data->get(self::CITE) : null;

        if (is_string($cite) && $cite !== '') {
            $parts[] = '<cite>' . Xml::escape($cite) . '</cite>';
        }

        $children = $childRenderer->renderNodes($node->children());

        if ($children !== '') {
            $parts[] = $children;
        }

        $contents = $parts === [] ? '' : "\n" . implode("\n", $parts) . "\n";

        return new HtmlElement('blockquote', ['title' => 'Zitat'], $contents);
    }
}
