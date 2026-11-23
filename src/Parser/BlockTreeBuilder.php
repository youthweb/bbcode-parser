<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Parser;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Node;
use Youthweb\BBCodeParser\Extension\Node\BBList;
use Youthweb\BBCodeParser\Extension\Node\BBListItem;
use Youthweb\BBCodeParser\Extension\Renderer\QuoteRenderer;

/**
 * Builds the block structure of the node tree: quotes, lists and their items, paragraphs and code.
 *
 * The parser tells it what it found in the text. The text between the tags is collected and
 * turned into paragraphs when a container opens or closes. The inline tags of a paragraph are
 * left to the callback.
 *
 * @experimental
 * @internal
 */
final class BlockTreeBuilder
{
    /** Quotes and lists may be nested up to this depth, a deeper tag stays text. */
    public const MAX_DEPTH = 5;

    /** Marks a raw text in the collected text, the number is the index of the raw text. */
    public const PLACEHOLDER = "\x1B";

    /** Finds the placeholders of raw texts, the group is the index of the raw text. */
    public const PLACEHOLDER_PATTERN = '~\x1B(\d+)\x1B~';

    /**
     * Open tags, the innermost last. A tag over the depth limit is on the stack without a node,
     * so that its closing tag pairs with it and stays text as well.
     *
     * @var list<array{name: string, node: ?Node, implicit: bool}>
     */
    private array $stack = [];

    private string $buffer = '';

    /** @var list<string> */
    private array $raws = [];

    /**
     * @param \Closure(string, Paragraph, list<string>): void $inlineParser parses the inline tags of a paragraph text
     */
    public function __construct(
        private readonly Document $document,
        private readonly \Closure $inlineParser,
    ) {}

    /**
     * Adds text that is parsed for inline tags.
     */
    public function addText(string $text): void
    {
        $this->buffer .= $text;
    }

    /**
     * Adds text that is taken as it is: it is neither split into paragraphs nor parsed.
     */
    public function addRaw(string $text): void
    {
        if ($text === '') {
            return;
        }

        $this->raws[] = $text;
        $this->buffer .= self::PLACEHOLDER . (count($this->raws) - 1) . self::PLACEHOLDER;
    }

    public function addCode(string $code): void
    {
        $this->flush();

        $block = new IndentedCode();
        $block->setLiteral($code);
        $this->current()->appendChild($block);
    }

    /**
     * Opens a quote; $literal is the text of the tag, if it is too deep.
     */
    public function openQuote(string $name, ?string $cite, string $literal): void
    {
        if ($this->isTooDeep()) {
            $this->openLiteral($name, $literal);

            return;
        }

        $quote = new BlockQuote();

        if ($cite !== null && $cite !== '') {
            $quote->data->set(QuoteRenderer::CITE, $cite);
        }

        $this->push($name, $quote);
    }

    /**
     * Opens a list with its first item. Text before the first `[*]` is the text of this item.
     */
    public function openList(?string $option, string $literal): void
    {
        if ($this->isTooDeep()) {
            $this->openLiteral('list', $literal);

            return;
        }

        $this->push('list', new BBList($option));
        $this->push('*', new BBListItem(), true);
    }

    /**
     * Starts a new item, if the tag is in a list. Otherwise it stays text.
     */
    public function nextItem(string $literal): void
    {
        $index = $this->innermostIndex();

        if ($index === null || $index !== count($this->stack) - 1 || ! in_array($this->stack[$index]['name'], ['list', '*'], true)) {
            // Not in a list, or in the text of a tag over the depth limit
            $this->buffer .= $literal;

            return;
        }

        if ($this->stack[$index]['implicit'] && trim($this->buffer) === '' && $this->stack[$index]['node']?->hasChildren() === false) {
            // The first item is still empty, the marker is its beginning.
            $this->stack[$index]['implicit'] = false;

            return;
        }

        if ($this->stack[$index]['name'] === '*') {
            $this->closeFrom($index);
        }

        $this->push('*', new BBListItem());
    }

    /**
     * Closes the innermost open tag of that name and everything inside it. Without one it stays text.
     */
    public function close(string $name, string $literal): void
    {
        for ($index = count($this->stack) - 1; $index >= 0; $index--) {
            if ($this->stack[$index]['name'] === $name) {
                if ($this->stack[$index]['node'] === null) {
                    // The closing tag of a tag over the depth limit: both stay text in the same paragraph.
                    $this->closeFrom($index + 1);
                    array_pop($this->stack);
                    $this->buffer .= $literal;

                    return;
                }

                $this->closeFrom($index);

                return;
            }
        }

        $this->buffer .= $literal;
    }

    /**
     * Closes everything that is still open.
     */
    public function finish(): void
    {
        $this->closeFrom(0);
        $this->flush();
    }

    private function openLiteral(string $name, string $literal): void
    {
        $this->buffer .= $literal;
        $this->stack[] = ['name' => $name, 'node' => null, 'implicit' => false];
    }

    private function isTooDeep(): bool
    {
        $depth = 0;

        foreach ($this->stack as $entry) {
            if ($entry['node'] instanceof BlockQuote || $entry['node'] instanceof BBList) {
                $depth++;
            }
        }

        return $depth >= self::MAX_DEPTH;
    }

    private function push(string $name, Node $node, bool $implicit = false): void
    {
        $this->flush();
        $this->current()->appendChild($node);
        $this->stack[] = ['name' => $name, 'node' => $node, 'implicit' => $implicit];
    }

    /**
     * Closes the tag at the index and all tags inside of it.
     */
    private function closeFrom(int $index): void
    {
        while (count($this->stack) > $index) {
            $this->flush();

            $entry = array_pop($this->stack);
            $node = $entry['node'];

            // An item that was only opened by `[list]`, and a list without items, leave no trace.
            if ($node !== null && ! $node->hasChildren() && ($entry['implicit'] || $node instanceof BBList)) {
                $node->detach();
            }
        }
    }

    /**
     * The index of the innermost open tag with a node.
     */
    private function innermostIndex(): ?int
    {
        for ($index = count($this->stack) - 1; $index >= 0; $index--) {
            if ($this->stack[$index]['node'] !== null) {
                return $index;
            }
        }

        return null;
    }

    private function current(): Node
    {
        $index = $this->innermostIndex();

        return $index === null ? $this->document : ($this->stack[$index]['node'] ?? $this->document);
    }

    private function flush(): void
    {
        $text = $this->buffer;
        $this->buffer = '';

        foreach (preg_split('~\n[ \t]*\n~', $text) ?: [] as $part) {
            if (trim($part) === '') {
                continue;
            }

            $paragraph = new Paragraph();
            ($this->inlineParser)(trim($part), $paragraph, $this->raws);
            $this->current()->appendChild($paragraph);
        }
    }
}
