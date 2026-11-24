<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Node\Size;
use Youthweb\BBCodeParser\Html;

/**
 * Renders `[size]` like the parser: the option as integer percent, 0 and non-numbers give 100, limited by the config.
 *
 * Without content nothing is rendered.
 *
 * @experimental
 * @internal
 */
final readonly class SizeRenderer implements NodeRendererInterface
{
    public function __construct(private Config $config) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Size) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $content = $childRenderer->renderNodes($node->children());

        if ($content === '') {
            return '';
        }

        $size = intval(trim($node->getOption() ?? ''));

        if ($size === 0) {
            $size = 100;
        }

        $size = max($size, $this->config->get('callbacks.size_param.min_size'));
        $size = min($size, $this->config->get('callbacks.size_param.max_size'));

        return Html::span($content, ['style' => 'font-size:' . $size . '%;']);
    }
}
