<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures\Extension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Node\Video;

/**
 * Models a video filter of a project: it embeds a youtube video and reads the config keys `embed_videos`,
 * `embed_maxwidth` and `embed_maxheight`. Anything else is left to the default renderer.
 */
final readonly class VideoFilterExtension implements ExtensionInterface, NodeRendererInterface
{
    public function __construct(private Config $config) {}

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addRenderer(Video::class, $this, 10);
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): ?string
    {
        if (! $node instanceof Video || ! $this->config->get('embed_videos')) {
            return null;
        }

        if (preg_match('~^https://youtu\.be/([\w-]{11})$~', trim($node->getLiteral()), $matches) !== 1) {
            return null;
        }

        return '<iframe width="' . (int) $this->config->get('embed_maxwidth') . '" height="' . (int) $this->config->get('embed_maxheight') . '" src="//www.youtube.com/embed/' . $matches[1] . '"></iframe>';
    }
}
