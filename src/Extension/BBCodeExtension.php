<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\CommonMark\Renderer\Block\IndentedCodeRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\EmphasisRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\LinkRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\StrongRenderer;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\Block\DocumentRenderer;
use League\CommonMark\Renderer\Block\ParagraphRenderer;
use League\CommonMark\Renderer\Inline\NewlineRenderer;
use League\CommonMark\Renderer\Inline\TextRenderer;
use Youthweb\BBCodeParser\Extension\Node\Underline;
use Youthweb\BBCodeParser\Extension\Renderer\UnderlineRenderer;

/**
 * Renderers for the node tree of the BBCode engine, without the Markdown extensions.
 *
 * @experimental
 * @internal
 */
final class BBCodeExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(Document::class, new DocumentRenderer())
            ->addRenderer(Paragraph::class, new ParagraphRenderer())
            ->addRenderer(Text::class, new TextRenderer())
            ->addRenderer(Newline::class, new NewlineRenderer())
            ->addRenderer(Strong::class, new StrongRenderer())
            ->addRenderer(Emphasis::class, new EmphasisRenderer())
            ->addRenderer(Underline::class, new UnderlineRenderer())
            ->addRenderer(Link::class, new LinkRenderer())
            ->addRenderer(IndentedCode::class, new IndentedCodeRenderer());
    }
}
