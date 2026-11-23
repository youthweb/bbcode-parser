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
use League\CommonMark\Extension\CommonMark\Renderer\Inline\StrongRenderer;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\Block\DocumentRenderer;
use League\CommonMark\Renderer\Block\ParagraphRenderer;
use League\CommonMark\Renderer\Inline\NewlineRenderer;
use League\CommonMark\Renderer\Inline\TextRenderer;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Node\Email;
use Youthweb\BBCodeParser\Extension\Node\Image;
use Youthweb\BBCodeParser\Extension\Node\Underline;
use Youthweb\BBCodeParser\Extension\Renderer\EmailRenderer;
use Youthweb\BBCodeParser\Extension\Renderer\ImageRenderer;
use Youthweb\BBCodeParser\Extension\Renderer\LinkRenderer;
use Youthweb\BBCodeParser\Extension\Renderer\UnderlineRenderer;

/**
 * Renderers for the node tree of the BBCode engine, without the Markdown extensions.
 *
 * @experimental
 * @internal
 */
final readonly class BBCodeExtension implements ExtensionInterface
{
    public function __construct(private Config $config) {}

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $linkRenderer = new LinkRenderer($this->config);

        $environment
            ->addRenderer(Document::class, new DocumentRenderer())
            ->addRenderer(Paragraph::class, new ParagraphRenderer())
            ->addRenderer(Text::class, new TextRenderer())
            ->addRenderer(Newline::class, new NewlineRenderer())
            ->addRenderer(Strong::class, new StrongRenderer())
            ->addRenderer(Emphasis::class, new EmphasisRenderer())
            ->addRenderer(Underline::class, new UnderlineRenderer())
            ->addRenderer(Link::class, $linkRenderer)
            ->addRenderer(Email::class, new EmailRenderer($this->config))
            ->addRenderer(Image::class, new ImageRenderer($this->config, $linkRenderer))
            ->addRenderer(IndentedCode::class, new IndentedCodeRenderer())
            ->addEventListener(DocumentParsedEvent::class, new AutolinkListener($this->config));
    }
}
