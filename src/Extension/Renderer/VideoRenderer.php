<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Youthweb\BBCodeParser\Extension\Node\Video;
use Youthweb\BBCodeParser\Html;

/**
 * Renders `[video]`, `[v]` and `[youtube]` like the parser without a video filter: as a link of the url.
 *
 * @internal
 */
final readonly class VideoRenderer implements NodeRendererInterface
{
    public function __construct(private LinkRenderer $linkRenderer) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Video) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $content = $node->getLiteral();

        return $this->linkRenderer->renderLink(
            option: '',
            contentHtml: Html::escapeSpecialChars($content),
            plain: $content,
            textOnly: true,
        );
    }
}
