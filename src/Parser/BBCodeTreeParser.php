<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Parser;

use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;

/**
 * Spike: parses BBCode into a league/commonmark node tree.
 *
 * @experimental
 */
final class BBCodeTreeParser
{
    private const TAG = '~\[(/)?(b|i|url|code)(?:=([^\]]*))?\]~i';

    public function parse(string $input): Document
    {
        $input = str_replace(["\r\n", "\r"], "\n", $input);
        $document = new Document();

        foreach ($this->splitCode($input) as [$isCode, $chunk]) {
            if ($isCode) {
                $code = new IndentedCode();
                $code->setLiteral($chunk);
                $document->appendChild($code);

                continue;
            }

            foreach (preg_split('~\n[ \t]*\n~', $chunk) ?: [] as $paragraphText) {
                if (trim($paragraphText) === '') {
                    continue;
                }

                $paragraph = new Paragraph();
                $this->parseInline(trim($paragraphText), $paragraph);
                $document->appendChild($paragraph);
            }
        }

        return $document;
    }

    /**
     * Splits the input into text chunks and raw [code] chunks.
     *
     * @return list<array{bool, string}>
     */
    private function splitCode(string $input): array
    {
        $chunks = [];

        while (preg_match('~\[code\]~i', $input, $open, PREG_OFFSET_CAPTURE) === 1) {
            $start = $open[0][1];
            $chunks[] = [false, substr($input, 0, $start)];
            $rest = substr($input, $start + 6);

            if (preg_match('~\[/code\]~i', $rest, $close, PREG_OFFSET_CAPTURE) === 1) {
                $chunks[] = [true, substr($rest, 0, $close[0][1])];
                $input = substr($rest, $close[0][1] + 7);
            } else {
                // Unclosed: the rest is code, like 1.x.
                $chunks[] = [true, $rest];
                $input = '';
            }
        }

        $chunks[] = [false, $input];

        return $chunks;
    }

    private function parseInline(string $text, Paragraph $paragraph): void
    {
        /** @var list<array{string, Node, ?string}> $stack */
        $stack = [];
        $current = $paragraph;
        $offset = 0;

        preg_match_all(self::TAG, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($matches as $match) {
            [$full, $pos] = $match[0];
            $closing = $match[1][0] === '/';
            $name = strtolower($match[2][0]);
            $arg = isset($match[3]) ? trim($match[3][0], " \t\"'") : null;

            $this->appendText($current, substr($text, $offset, $pos - $offset));
            $offset = $pos + strlen($full);

            if (! $closing) {
                $node = $this->createNode($name, $arg);
                $current->appendChild($node);
                $stack[] = [$name, $node, $arg];
                $current = $node;

                continue;
            }

            $index = $this->findOpen($stack, $name);

            if ($index === null) {
                // A closing tag without an opening one stays literal.
                $this->appendText($current, $full);

                continue;
            }

            // Close the inner tags implicitly, then the tag itself.
            while (count($stack) > $index) {
                [$closedName, $closedNode, $closedArg] = array_pop($stack);

                if ($closedName === 'url' && $closedArg === null && $closedNode instanceof Link) {
                    $closedNode->setUrl($this->plainText($closedNode));
                }
            }

            $current = $stack === [] ? $paragraph : $stack[count($stack) - 1][1];
        }

        $this->appendText($current, substr($text, $offset));

        // Tags that stay open are closed at the end.
        foreach (array_reverse($stack) as [$closedName, $closedNode, $closedArg]) {
            if ($closedName === 'url' && $closedArg === null && $closedNode instanceof Link) {
                $closedNode->setUrl($this->plainText($closedNode));
            }
        }
    }

    /**
     * @param list<array{string, Node, ?string}> $stack
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
        switch ($name) {
            case 'b':
                return new Strong();
            case 'i':
                return new Emphasis();
            default:
                return new Link($arg ?? '');
        }
    }

    private function appendText(Node $parent, string $text): void
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
                $parent->appendChild(new Text($line));
            }
        }
    }

    private function plainText(Node $node): string
    {
        $text = '';

        foreach ($node->children() as $child) {
            if ($child instanceof Text) {
                $text .= $child->getLiteral();
            }
        }

        return $text;
    }
}
