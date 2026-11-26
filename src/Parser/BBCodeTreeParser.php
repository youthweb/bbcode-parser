<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Parser;

use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use Youthweb\BBCodeParser\Extension\TextReplacementListener;
use Youthweb\BBCodeParser\Extension\Node\Color;
use Youthweb\BBCodeParser\Extension\Node\Email;
use Youthweb\BBCodeParser\Extension\Node\Image;
use Youthweb\BBCodeParser\Extension\Node\Size;
use Youthweb\BBCodeParser\Extension\Node\Underline;
use Youthweb\BBCodeParser\Extension\Node\Video;

/**
 * Spike: parses BBCode into a league/commonmark node tree.
 *
 * @experimental
 */
final class BBCodeTreeParser
{
    /** The tag takes no option: with an option it stays text, like in `Manager`. */
    private const NO_OPTION = 'none';

    /** The tag works with and without an option. */
    private const OPTIONAL = 'optional';

    /** The tag works only with an option: without one it stays text. */
    private const REQUIRED = 'required';

    /**
     * Tags with their node, their option and whether the content is raw text.
     * `f` and `k` are the deprecated aliases of `b` and `i`, `pic` is an alias of `img`.
     * Like in `Manager` a closing tag pairs only with an opening tag of the same name.
     *
     * @var array<string, array{node: class-string<Node>, option: string, raw: bool}>
     */
    private const TAGS = [
        'b' => ['node' => Strong::class, 'option' => self::NO_OPTION, 'raw' => false],
        'f' => ['node' => Strong::class, 'option' => self::NO_OPTION, 'raw' => false],
        'i' => ['node' => Emphasis::class, 'option' => self::NO_OPTION, 'raw' => false],
        'k' => ['node' => Emphasis::class, 'option' => self::NO_OPTION, 'raw' => false],
        'u' => ['node' => Underline::class, 'option' => self::NO_OPTION, 'raw' => false],
        'url' => ['node' => Link::class, 'option' => self::OPTIONAL, 'raw' => false],
        'ywlink' => ['node' => Link::class, 'option' => self::REQUIRED, 'raw' => false],
        'email' => ['node' => Email::class, 'option' => self::OPTIONAL, 'raw' => false],
        'img' => ['node' => Image::class, 'option' => self::NO_OPTION, 'raw' => true],
        'pic' => ['node' => Image::class, 'option' => self::NO_OPTION, 'raw' => true],
        'color' => ['node' => Color::class, 'option' => self::OPTIONAL, 'raw' => false],
        'size' => ['node' => Size::class, 'option' => self::OPTIONAL, 'raw' => false],
        'video' => ['node' => Video::class, 'option' => self::NO_OPTION, 'raw' => true],
        'v' => ['node' => Video::class, 'option' => self::NO_OPTION, 'raw' => true],
        'youtube' => ['node' => Video::class, 'option' => self::NO_OPTION, 'raw' => true],
    ];

    /**
     * @param bool $parseHeadlines whether `[h1]` to `[h6]` are tags, like the config `parse_headlines`
     */
    public function __construct(private readonly bool $parseHeadlines = false) {}

    public function parse(string $input): Document
    {
        // The control character marks raw text while parsing, so it cannot come from the input.
        $input = str_replace(["\r\n", "\r", BlockTreeBuilder::PLACEHOLDER], ["\n", "\n", ''], $input);
        $document = new Document();
        $builder = new BlockTreeBuilder($document, $this->parseInline(...));
        $offset = 0;

        $blockTags = 'quote|q|z|list|code|noparse|\*' . ($this->parseHeadlines ? '|h[1-6]' : '');

        preg_match_all(
            '~\[(/)?(' . $blockTags . ')(?:=([^\]]*))?\]~i',
            $input,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE,
        );

        foreach ($matches as $match) {
            [$full, $pos] = $match[0];

            if ($pos < $offset) {
                // Inside of the raw content of a tag
                continue;
            }

            $builder->addText(substr($input, $offset, $pos - $offset));
            $offset = $pos + strlen($full);

            $closing = $match[1][0] === '/';
            $name = strtolower($match[2][0]);
            $arg = isset($match[3]) ? trim($match[3][0], " \t\"'") : null;

            if (! $closing && $name !== 'noparse' && $builder->isInHeading()) {
                // A heading holds inline content only: blocks and further headings stay text.
                $builder->addText($full);

                continue;
            }

            if ($name === 'code' || $name === 'noparse') {
                if ($closing || $arg !== null) {
                    // A closing tag without an opening one, or an option the tag does not take, stays text.
                    $builder->addText($full);

                    continue;
                }

                $end = strlen($input);
                $next = $end;

                if (preg_match('~\[/' . $name . '\]~i', $input, $close, PREG_OFFSET_CAPTURE, $offset) === 1) {
                    $end = $close[0][1];
                    $next = $end + strlen($close[0][0]);
                }

                // Unclosed: the rest is raw, like `Manager`.
                $raw = substr($input, $offset, $end - $offset);
                $offset = $next;

                if ($name === 'code') {
                    $builder->addCode($raw);
                } else {
                    $builder->addRaw($raw);
                }

                continue;
            }

            if ($closing) {
                $builder->close($name, $full);
            } elseif ($name === '*') {
                $arg === null ? $builder->nextItem($full) : $builder->addText($full);
            } elseif ($name === 'list') {
                $builder->openList($arg, $full);
            } elseif ($name[0] === 'h') {
                // Without an option and with a closing tag later on, otherwise it stays text.
                if ($arg === null && preg_match('~\[/' . $name . '\]~i', $input, $close, 0, $offset) === 1) {
                    $builder->openHeading((int) $name[1], $name);
                } else {
                    $builder->addText($full);
                }
            } else {
                $builder->openQuote($name, $arg, $full);
            }
        }

        $builder->addText(substr($input, $offset));
        $builder->finish();

        return $document;
    }

    /**
     * @param list<string> $raws the raw texts, the placeholders in the text point to them
     */
    private function parseInline(string $text, AbstractBlock $paragraph, array $raws): void
    {
        /** @var list<array{string, Node}> $stack */
        $stack = [];
        $current = $paragraph;
        $offset = 0;

        $pattern = '~\[(/)?(' . implode('|', array_keys(self::TAGS)) . ')(?:=([^\]]*))?\]~i';

        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($matches as $match) {
            [$full, $pos] = $match[0];
            $closing = $match[1][0] === '/';
            $name = strtolower($match[2][0]);
            $arg = isset($match[3]) ? trim($match[3][0], " \t\"'") : null;

            if ($pos < $offset) {
                // Inside of the raw content of a tag
                continue;
            }

            $this->appendText($current, substr($text, $offset, $pos - $offset), $raws);
            $offset = $pos + strlen($full);

            if (! $closing && ! $this->acceptsOption($name, $arg)) {
                // `Manager` keeps the text of a tag with an option it does not take, or without one it needs.
                $this->appendText($current, $full, $raws);

                continue;
            }

            if (! $closing && self::TAGS[$name]['raw']) {
                $offset = $this->appendRaw($current, $name, $text, $offset);

                continue;
            }

            if (! $closing) {
                $node = $this->createNode($name, $arg);
                $current->appendChild($node);
                $stack[] = [$name, $node];
                $current = $node;

                continue;
            }

            $index = $this->findOpen($stack, $name);

            if ($index === null) {
                // A closing tag without an opening one stays literal.
                $this->appendText($current, $full, $raws);

                continue;
            }

            // Close the inner tags implicitly, then the tag itself.
            array_splice($stack, $index);

            $current = $stack === [] ? $paragraph : $stack[count($stack) - 1][1];
        }

        $this->appendText($current, substr($text, $offset), $raws);
    }

    /**
     * @param list<array{string, Node}> $stack
     */
    private function findOpen(array $stack, string $name): ?int
    {
        for ($i = count($stack) - 1; $i >= 0; $i--) {
            if ($stack[$i][0] === $name) {
                return $i;
            }
        }

        return null;
    }

    private function createNode(string $name, ?string $arg): Node
    {
        $class = self::TAGS[$name]['node'];

        return match ($class) {
            Link::class => new Link($arg ?? ''),
            Email::class => new Email($arg ?? ''),
            Color::class => new Color($arg),
            Size::class => new Size($arg),
            default => new $class(),
        };
    }

    private function acceptsOption(string $name, ?string $arg): bool
    {
        return match (self::TAGS[$name]['option']) {
            self::NO_OPTION => $arg === null,
            self::REQUIRED => $arg !== null,
            default => true,
        };
    }

    /**
     * Appends a tag with raw content: everything up to the closing tag, or to the end if it is missing, like `Manager`.
     *
     * @return int the offset after the closing tag
     */
    private function appendRaw(Node $parent, string $name, string $text, int $offset): int
    {
        $end = strlen($text);
        $next = $end;

        if (preg_match('~\[/' . $name . '\]~i', $text, $close, PREG_OFFSET_CAPTURE, $offset) === 1) {
            $end = $close[0][1];
            $next = $end + strlen($close[0][0]);
        }

        $node = self::TAGS[$name]['node'] === Video::class ? new Video() : new Image();
        $node->setLiteral(substr($text, $offset, $end - $offset));
        $parent->appendChild($node);

        return $next;
    }

    /**
     * @param list<string> $raws
     */
    private function appendText(Node $parent, string $text, array $raws): void
    {
        // Odd parts are the numbers of the placeholders of raw texts
        $parts = preg_split(BlockTreeBuilder::PLACEHOLDER_PATTERN, $text, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];

        foreach ($parts as $i => $part) {
            $this->appendLines($parent, $i % 2 === 1 ? ($raws[(int) $part] ?? '') : $part, $i % 2 === 1);
        }
    }

    private function appendLines(Node $parent, string $text, bool $raw): void
    {
        if ($text === '') {
            return;
        }

        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if ($i > 0) {
                $parent->appendChild(new Newline(Newline::SOFTBREAK));
            }

            if ($line !== '') {
                $node = new Text($line);

                if ($raw) {
                    $node->data->set(TextReplacementListener::RAW, true);
                }

                $parent->appendChild($node);
            }
        }
    }
}
