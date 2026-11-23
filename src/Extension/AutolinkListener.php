<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension;

use Art4\UrlLinker\UrlLinker;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Node\Email;
use Youthweb\BBCodeParser\Extension\Renderer\LinkRenderer;

/**
 * Turns the bare urls in the text nodes into links, like the url visitor of 1.x.
 *
 * Only the public API of urllinker is used: its creators return markers, and the markers are split into nodes.
 * Email addresses stay text. Text inside a link or an email tag is left alone.
 *
 * @experimental
 * @internal
 */
final readonly class AutolinkListener
{
    /** Key in the data of a text that is left alone, like the content of `[noparse]` */
    public const RAW = 'raw';

    private const MARKER = "\x1A";

    public function __construct(private Config $config) {}

    public function __invoke(DocumentParsedEvent $event): void
    {
        if (! $this->config->get('parse_urls')) {
            return;
        }

        $texts = [];

        foreach ($event->getDocument()->iterator() as $node) {
            if ($node instanceof Text && ! $node->data->has(self::RAW) && ! $this->isInsideLink($node)) {
                $texts[] = $node;
            }
        }

        foreach ($texts as $text) {
            $this->link($text);
        }
    }

    private function isInsideLink(Text $text): bool
    {
        for ($parent = $text->parent(); $parent instanceof \League\CommonMark\Node\Node; $parent = $parent->parent()) {
            if ($parent instanceof Link || $parent instanceof Email) {
                return true;
            }
        }

        return false;
    }

    private function link(Text $text): void
    {
        $literal = $text->getLiteral();

        if (str_contains($literal, self::MARKER)) {
            return;
        }

        /** @var list<array{bool, string}> $tokens */
        $tokens = [];

        $marker = static function (bool $isLink, string $value) use (&$tokens): string {
            $tokens[] = [$isLink, $value];

            return self::MARKER . (count($tokens) - 1) . self::MARKER;
        };

        $linker = new UrlLinker([
            'htmlLinkCreator' => static fn(string $url): string => $marker(true, $url),
            'emailLinkCreator' => static fn(string $email): string => $marker(false, $email),
        ]);

        $html = $linker->linkUrlsAndEscapeHtml($literal);

        if ($tokens === []) {
            return;
        }

        $parts = preg_split('~' . self::MARKER . '(\d+)' . self::MARKER . '~', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($parts ?: [] as $i => $part) {
            if ($i % 2 === 0) {
                if ($part !== '') {
                    $text->insertBefore(new Text(html_entity_decode($part, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                }

                continue;
            }

            [$isLink, $value] = $tokens[(int) $part];

            if (! $isLink) {
                $text->insertBefore(new Text($value));

                continue;
            }

            $link = new Link($value);
            $link->data->set(LinkRenderer::AUTOLINK, true);
            $link->appendChild(new Text($value));
            $text->insertBefore($link);
        }

        $text->detach();
    }
}
