<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Node\Color;
use Youthweb\BBCodeParser\Html;

/**
 * Renders `[color]` like 1.x: a name of the allowed colors or `#rrggbb`, anything else gives the default color.
 *
 * Without content nothing is rendered.
 *
 * @experimental
 * @internal
 */
final readonly class ColorRenderer implements NodeRendererInterface
{
    public function __construct(private Config $config) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Color) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $content = $childRenderer->renderNodes($node->children());

        if ($content === '') {
            return '';
        }

        $option = trim($node->getOption() ?? '');

        $allowedColors = (array) $this->config->get('callbacks.color_param.allowed_colors');
        $color = mb_convert_case($option, MB_CASE_TITLE);

        if (! in_array($color, $allowedColors, true)) {
            if (preg_match('~^#[a-f0-9]{6}$~i', $option) === 1) {
                $color = $option;
            } else {
                $color = $this->config->get('callbacks.color_param.default_color');
            }
        }

        return Html::span($content, ['style' => 'color:' . $color . ';']);
    }
}
