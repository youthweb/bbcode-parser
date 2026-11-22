<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Parser;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\CommonMark\Renderer\Block\IndentedCodeRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\EmphasisRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\LinkRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\StrongRenderer;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\Block\DocumentRenderer;
use League\CommonMark\Renderer\Block\ParagraphRenderer;
use League\CommonMark\Renderer\HtmlRenderer;
use League\CommonMark\Renderer\Inline\NewlineRenderer;
use League\CommonMark\Renderer\Inline\TextRenderer;

/**
 * Spike: renders a node tree with an environment that has no Markdown extension.
 *
 * @experimental
 */
final class SpikeHtmlRenderer
{
    private HtmlRenderer $renderer;

    public function __construct()
    {
        $environment = new Environment([
            'allow_unsafe_links' => false,
            'renderer' => ['soft_break' => "<br />\n", 'block_separator' => "\n", 'inner_separator' => "\n"],
        ]);

        $environment->addRenderer(Document::class, new DocumentRenderer());
        $environment->addRenderer(Paragraph::class, new ParagraphRenderer());
        $environment->addRenderer(Text::class, new TextRenderer());
        $environment->addRenderer(Newline::class, new NewlineRenderer());
        $environment->addRenderer(Strong::class, new StrongRenderer());
        $environment->addRenderer(Emphasis::class, new EmphasisRenderer());
        $environment->addRenderer(Link::class, new LinkRenderer());
        $environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer());

        $this->renderer = new HtmlRenderer($environment);
    }

    public function render(Document $document): string
    {
        return rtrim($this->renderer->renderDocument($document)->getContent());
    }
}
