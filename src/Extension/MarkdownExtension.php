<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Parser\Block\BlockQuoteStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\FencedCodeStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\HeadingStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\IndentedCodeStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\ListBlockStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\ThematicBreakStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\AutolinkParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\BacktickParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\BangParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\CloseBracketParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\EntityParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\EscapableParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\OpenBracketParser;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\CodeRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ThematicBreakRenderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\Strikethrough\Strikethrough;
use League\CommonMark\Extension\Strikethrough\StrikethroughDelimiterProcessor;
use League\CommonMark\Extension\Strikethrough\StrikethroughRenderer;
use League\CommonMark\Parser\Inline\NewlineParser;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Parser\DepthLimitedStartParser;
use Youthweb\BBCodeParser\Extension\Renderer\HeadingRenderer;

/**
 * The parsers of the Markdown input and the renderers of the constructs that BBCode does not have.
 *
 * It has CommonMark without raw HTML: there is no parser for it, so `<` stays text and is escaped.
 * Tables, task lists, footnotes and attributes are not part of it. Headings and thematic breaks can
 * be switched off with the config keys `markdown.headings` and `markdown.thematic_breaks`.
 * Quotes and lists are nested at most as deep as in the BBCode mode.
 *
 * @internal
 */
final readonly class MarkdownExtension implements ConfigurableExtensionInterface
{
    /** Quotes and lists may be nested up to this depth, like in the BBCode mode. */
    public const MAX_DEPTH = 5;

    /** The priority of the listener that builds the tree: it runs before the autolinker and the listeners of a project. */
    public const TREE_PRIORITY = 110;

    public function __construct(private Config $config) {}

    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        // The list parser and the emphasis processor read their settings from here.
        $builder->addSchema('commonmark', Expect::structure([
            'enable_strong' => Expect::bool(true),
            'enable_em' => Expect::bool(true),
            'unordered_list_markers' => Expect::listOf('string')->min(1)->default(['*', '+', '-'])->mergeDefaults(false),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addBlockStartParser(new DepthLimitedStartParser(new BlockQuoteStartParser(), self::MAX_DEPTH), 70)
            ->addBlockStartParser(new FencedCodeStartParser(), 50)
            ->addBlockStartParser(new DepthLimitedStartParser(new ListBlockStartParser(), self::MAX_DEPTH), 10)
            ->addBlockStartParser(new IndentedCodeStartParser(), -100)
            ->addInlineParser(new NewlineParser(), 200)
            ->addInlineParser(new BacktickParser(), 150)
            ->addInlineParser(new EscapableParser(), 80)
            ->addInlineParser(new EntityParser(), 70)
            ->addInlineParser(new AutolinkParser(), 50)
            ->addInlineParser(new CloseBracketParser(), 30)
            ->addInlineParser(new OpenBracketParser(), 20)
            ->addInlineParser(new BangParser(), 10)
            ->addDelimiterProcessor(new EmphasisDelimiterProcessor('*'))
            ->addDelimiterProcessor(new EmphasisDelimiterProcessor('_'))
            ->addDelimiterProcessor(new StrikethroughDelimiterProcessor())
            ->addRenderer(Code::class, new CodeRenderer())
            ->addRenderer(Strikethrough::class, new StrikethroughRenderer())
            ->addEventListener(DocumentParsedEvent::class, new MarkdownTreeListener(), self::TREE_PRIORITY);

        if ($this->config->get('markdown.headings', true)) {
            $environment
                ->addBlockStartParser(new HeadingStartParser(), 60)
                ->addRenderer(Heading::class, new HeadingRenderer($this->config));
        }

        if ($this->config->get('markdown.thematic_breaks', true)) {
            $environment
                ->addBlockStartParser(new ThematicBreakStartParser(), 20)
                ->addRenderer(ThematicBreak::class, new ThematicBreakRenderer());
        }
    }
}
