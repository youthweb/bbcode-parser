<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\RegexHelper;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Html;

/**
 * Renders `[url]`, `[YWLINK]` and autolinked urls like the parser: an invalid or unsafe url leaves the plain content.
 *
 * @internal
 */
final readonly class LinkRenderer implements NodeRendererInterface
{
    /**
     * The data key of a link that the autolinker found: the url scanner has validated it already.
     */
    public const AUTOLINK = 'autolink';

    public function __construct(private Config $config) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Link) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $textOnly = true;
        $plain = '';

        foreach ($node->children() as $child) {
            if ($child instanceof Text) {
                $plain .= $child->getLiteral();
            } else {
                $textOnly = false;
            }
        }

        return $this->renderLink(
            $node->getUrl(),
            $childRenderer->renderNodes($node->children()),
            $plain,
            $textOnly,
            $node->data->get(self::AUTOLINK, false) === true,
        );
    }

    /**
     * @param string $option      the url of the option, empty if the url is the content
     * @param string $contentHtml the rendered content
     * @param string $plain       the content as text
     * @param bool   $textOnly    whether the content has nothing but text
     * @param bool   $scanned     whether the url scanner found the url, then it is not validated again
     */
    public function renderLink(string $option, string $contentHtml, string $plain, bool $textOnly, bool $scanned = false): string
    {
        $option = trim($option);

        if ($contentHtml === '' && $option === '') {
            return '';
        }

        $url = $option === '' ? $plain : $option;

        // Put http:// in front, if nothing is given
        if (preg_match('~^[a-z]+://~i', $url) !== 1) {
            $url = 'http://' . $url;
        }

        // An invalid or unsafe url shows only the text
        if (! $scanned && ! $this->config->getValidation()->isValidUrl($url)) {
            return $contentHtml;
        }

        if (RegexHelper::isLinkPotentiallyUnsafe($url)) {
            return $contentHtml;
        }

        // Only a text that is the url itself may be shortened
        if ($textOnly && $plain === $url) {
            $contentHtml = Html::escapeSpecialChars(Html::shortenUrlText($plain, $this->config));
        }

        return Html::anchorFromConfig($url, $contentHtml, $this->config);
    }
}
