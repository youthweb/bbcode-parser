<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension;

use Art4\UrlLinker\UrlLinker;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Renderer\LinkRenderer;

/**
 * Turns the bare urls in the text nodes into links, like the url visitor of the parser.
 *
 * Only the public API of urllinker is used: its creators return markers, and the markers are split into nodes.
 * Email addresses stay text. Text inside a link or an email tag is left alone.
 *
 * @experimental
 * @internal
 */
final class AutolinkListener extends TextReplacementListener
{
    private const MARKER = "\x1A";

    public function __construct(private readonly Config $config) {}

    public function __invoke(DocumentParsedEvent $event): void
    {
        if (! $this->config->get('parse_urls')) {
            return;
        }

        parent::__invoke($event);
    }

    protected function replace(string $text): array
    {
        if (str_contains($text, self::MARKER)) {
            return [$text];
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

        $html = $linker->linkUrlsAndEscapeHtml($text);

        if ($tokens === []) {
            return [$text];
        }

        $result = [];
        $parts = preg_split('~' . self::MARKER . '(\d+)' . self::MARKER . '~', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($parts ?: [] as $i => $part) {
            if ($i % 2 === 0) {
                if ($part !== '') {
                    $result[] = html_entity_decode($part, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }

                continue;
            }

            [$isLink, $value] = $tokens[(int) $part];

            if (! $isLink) {
                $result[] = $value;

                continue;
            }

            $link = new Link($value);
            $link->data->set(LinkRenderer::AUTOLINK, true);
            $link->appendChild(new Text($value));
            $result[] = $link;
        }

        return $result;
    }
}
