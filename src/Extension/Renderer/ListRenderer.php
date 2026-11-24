<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Youthweb\BBCodeParser\Extension\Node\BBList;
use Youthweb\BBCodeParser\Extension\Node\BBListItem;
use Youthweb\BBCodeParser\Html;

/**
 * Renders a list with the options of the parser: `a`, `A`, `i`, `I` and a number give an ordered list, anything else a bullet list.
 *
 * A bullet list has no `type` attribute, it is obsolete in HTML and `disc` is the default.
 *
 * An item with a single paragraph is tight: the paragraph is rendered without `<p>`.
 *
 * @experimental
 * @internal
 */
final class ListRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof BBList) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $items = [];

        foreach ($node->children() as $item) {
            if ($item instanceof BBListItem) {
                $items[] = $this->renderItem($item, $childRenderer);
            }
        }

        $option = $node->getOption() ?? '';

        if (in_array($option, ['a', 'A', 'i', 'I'], true)) {
            return rtrim(Html::ol($items, ['type' => $option]));
        }

        if (preg_match('/^-?\d+$/', $option) === 1) {
            return rtrim(Html::ol($items, ['start' => (int) $option]));
        }

        return rtrim(Html::ul($items));
    }

    private function renderItem(BBListItem $item, ChildNodeRendererInterface $childRenderer): string
    {
        $paragraphs = 0;

        foreach ($item->children() as $child) {
            if ($child instanceof Paragraph) {
                $paragraphs++;
            }
        }

        if ($paragraphs !== 1) {
            return $childRenderer->renderNodes($item->children());
        }

        $parts = [];

        foreach ($item->children() as $child) {
            $parts[] = $child instanceof Paragraph
                ? $childRenderer->renderNodes($child->children())
                : $childRenderer->renderNodes([$child]);
        }

        return implode("\n", $parts);
    }
}
