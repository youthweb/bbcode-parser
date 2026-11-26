<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Parser;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Renderer\HtmlRenderer;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\BBCodeExtension;

/**
 * Renders a node tree with an environment that has no Markdown extension.
 *
 * @internal
 */
final readonly class BBCodeHtmlRenderer
{
    private Environment $environment;

    private HtmlRenderer $renderer;

    /**
     * @param iterable<ExtensionInterface> $extensions extensions of the project, added after the extension of the engine
     */
    public function __construct(?Config $config = null, iterable $extensions = [])
    {
        $environment = new Environment([
            'allow_unsafe_links' => false,
            'renderer' => ['soft_break' => "<br />\n", 'block_separator' => "\n", 'inner_separator' => "\n"],
        ]);

        $environment->addExtension(new BBCodeExtension($config ?? new Config()));

        foreach ($extensions as $extension) {
            $environment->addExtension($extension);
        }

        $this->environment = $environment;
        $this->renderer = new HtmlRenderer($environment);
    }

    public function render(Document $document): string
    {
        $this->environment->dispatch(new DocumentParsedEvent($document));

        return rtrim($this->renderer->renderDocument($document)->getContent());
    }
}
