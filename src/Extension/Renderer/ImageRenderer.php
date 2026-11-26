<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\RegexHelper;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Node\Image;
use Youthweb\BBCodeParser\Html;

/**
 * Renders `[img]` and `[PIC]` like the parser, without the image filter.
 *
 * @internal
 */
final readonly class ImageRenderer implements NodeRendererInterface
{
    public function __construct(
        private Config $config,
        private LinkRenderer $linkRenderer,
    ) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Image) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $content = $node->getLiteral();
        $contentHtml = Html::escapeSpecialChars($content);

        // Link the image, if it is not inside of a [url]
        $useAnchor = true;

        for ($parent = $node->parent(); $parent instanceof \League\CommonMark\Node\Node; $parent = $parent->parent()) {
            if ($parent instanceof Link) {
                $useAnchor = false;

                break;
            }
        }

        $url = trim($content);

        // Put http:// in front, if nothing is given
        if ($url !== '' && parse_url($url, PHP_URL_SCHEME) === null) {
            $url = 'http://' . $url;
        }

        // An invalid or unsafe url shows only the text
        if ($url === '' || ! $this->config->getValidation()->isValidUrl($url) || RegexHelper::isLinkPotentiallyUnsafe($url)) {
            return $contentHtml;
        }

        // Does the url really point to an image? If not, link it only
        if ($this->config->get('callbacks.image.force_check') && ! $this->config->getValidation()->isValidImageUrl($url)) {
            if (! $useAnchor) {
                return $contentHtml;
            }

            return $this->linkRenderer->renderLink('', $contentHtml, $content, true);
        }

        $image = Html::img($url, [
            'class' => 'img-responsive',
            'border' => '0',
        ]);

        if (! $useAnchor) {
            return $image;
        }

        return Html::anchor($url, $image, ['target' => '_blank']);
    }
}
