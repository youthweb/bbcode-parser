<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures\Extension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Youthweb\BBCodeParser\Extension\Node\Image;

/**
 * Models an image filter of a project: an image id becomes an image of the project, anything else is left to the default renderer.
 */
final class ImageFilterExtension implements ExtensionInterface, NodeRendererInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addRenderer(Image::class, $this, 10);
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): ?string
    {
        if (! $node instanceof Image) {
            return null;
        }

        $id = trim($node->getLiteral());

        if (! ctype_digit($id)) {
            // Like the FilterException of 1.x
            return null;
        }

        $img = '<img class="img-responsive" src="https://files.example.org/' . $id . '.png" />';

        for ($parent = $node->parent(); $parent instanceof Node; $parent = $parent->parent()) {
            if ($parent instanceof Link) {
                return $img;
            }
        }

        return '<a href="https://files.example.org/' . $id . '/full.png" target="_blank">' . $img . '</a>';
    }
}
