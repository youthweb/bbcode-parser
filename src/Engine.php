<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use Youthweb\BBCodeParser\Extension\BBCodeExtension;
use Youthweb\BBCodeParser\Extension\MarkdownExtension;
use Youthweb\BBCodeParser\Parser\BBCodeTreeParser;
use Youthweb\BBCodeParser\Parser\SpikeHtmlRenderer;

/**
 * Renders a text with the engine that is built on league/commonmark.
 *
 * Both inputs end in the same node tree and are rendered by the same renderers.
 * The mode is chosen per call, a text is either BBCode or Markdown.
 *
 * A project adds its own league extensions (parsers, listeners, renderers) with the second argument. They work
 * in both modes and are added after the extensions of the engine, so a renderer with a higher priority than 0
 * replaces a default renderer and a listener with the default priority runs after the listeners of the engine.
 *
 * @experimental
 */
final class Engine
{
    private readonly Config $config;

    /** @var list<ExtensionInterface> */
    private readonly array $extensions;

    private ?BBCodeTreeParser $bbcodeParser = null;

    private ?SpikeHtmlRenderer $bbcodeRenderer = null;

    private ?MarkdownParser $markdownParser = null;

    private ?HtmlRenderer $markdownRenderer = null;

    /**
     * @param iterable<ExtensionInterface> $extensions extensions of the project
     */
    public function __construct(?Config $config = null, iterable $extensions = [])
    {
        $this->config = $config ?? new Config();
        $this->extensions = array_values(is_array($extensions) ? $extensions : iterator_to_array($extensions, false));
    }

    public function toHtml(string $input, InputMode $mode = InputMode::BBCode): string
    {
        return match ($mode) {
            InputMode::BBCode => $this->bbcodeToHtml($input),
            InputMode::Markdown => $this->markdownToHtml($input),
        };
    }

    private function bbcodeToHtml(string $input): string
    {
        $this->bbcodeParser ??= new BBCodeTreeParser();
        $this->bbcodeRenderer ??= new SpikeHtmlRenderer($this->config, $this->extensions);

        return $this->bbcodeRenderer->render($this->bbcodeParser->parse($input));
    }

    private function markdownToHtml(string $input): string
    {
        if (! $this->markdownParser instanceof MarkdownParser || ! $this->markdownRenderer instanceof HtmlRenderer) {
            $environment = new Environment([
                'allow_unsafe_links' => false,
                'renderer' => [
                    'soft_break' => (string) $this->config->get('soft_break', '<br />'),
                    'block_separator' => "\n",
                    'inner_separator' => "\n",
                ],
            ]);

            $environment->addExtension(new BBCodeExtension($this->config));
            $environment->addExtension(new MarkdownExtension($this->config));

            foreach ($this->extensions as $extension) {
                $environment->addExtension($extension);
            }

            $this->markdownParser = new MarkdownParser($environment);
            $this->markdownRenderer = new HtmlRenderer($environment);
        }

        // The parser dispatches the event that the listeners of the extensions work on.
        $document = $this->markdownParser->parse($input);

        return rtrim($this->markdownRenderer->renderDocument($document)->getContent());
    }
}
