<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Parser;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

/**
 * Starts a quote or a list only if fewer than `$maxDepth` quotes and lists are open around it.
 *
 * The line stays text otherwise. A quote and a list count one level each, like the tags in the BBCode mode.
 *
 * @experimental
 * @internal
 */
final readonly class DepthLimitedStartParser implements BlockStartParserInterface, ConfigurationAwareInterface
{
    public function __construct(
        private BlockStartParserInterface $parser,
        private int $maxDepth,
    ) {}

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        if ($this->parser instanceof ConfigurationAwareInterface) {
            $this->parser->setConfiguration($configuration);
        }
    }

    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        $block = $parserState->getActiveBlockParser()->getBlock();

        // The next item of an open list is on the same level as the list
        if ($block instanceof ListBlock) {
            $block = $block->parent();
        }

        $depth = 0;

        while ($block instanceof \League\CommonMark\Node\Node) {
            if ($block instanceof BlockQuote || $block instanceof ListBlock) {
                $depth++;
            }

            $block = $block->parent();
        }

        if ($depth >= $this->maxDepth) {
            return BlockStart::none();
        }

        return $this->parser->tryStart($cursor, $parserState);
    }
}
