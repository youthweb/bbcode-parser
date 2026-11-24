<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use Youthweb\BBCodeParser\Extension\Node\Email;

/**
 * Base class for a listener that replaces parts of the text, for example smileys or mentions of users.
 *
 * Register it on the `DocumentParsedEvent` of an extension. It hands the text of every text node to
 * `replace()` and puts the result in its place. Text that is left alone: the content of `[noparse]`,
 * and text inside of a link or an `[email]`, so a replacement never lands inside of an anchor. Code
 * blocks and inline code are no text nodes.
 *
 * The engine runs its own listeners first: bare urls are links already when a listener with the default
 * priority runs. A higher priority runs earlier.
 *
 * @experimental
 */
abstract class TextReplacementListener
{
    /** Key in the data of a text that is left alone, like the content of `[noparse]` */
    public const RAW = 'raw';

    public function __invoke(DocumentParsedEvent $event): void
    {
        $texts = [];

        foreach ($event->getDocument()->iterator() as $node) {
            if ($node instanceof Text && ! $node->data->has(self::RAW) && ! $this->isInsideLink($node)) {
                $texts[] = $node;
            }
        }

        foreach ($texts as $text) {
            $this->replaceText($text);
        }
    }

    /**
     * Replaces parts of a text.
     *
     * @param string $text the text of one text node
     *
     * @return list<Node|string> the parts that take the place of the text: a string stays text, a node is put in as it is.
     *                           Return `[$text]` to leave the text as it is.
     */
    abstract protected function replace(string $text): array;

    private function isInsideLink(Text $text): bool
    {
        for ($parent = $text->parent(); $parent instanceof Node; $parent = $parent->parent()) {
            if ($parent instanceof Link || $parent instanceof Email) {
                return true;
            }
        }

        return false;
    }

    private function replaceText(Text $text): void
    {
        $literal = $text->getLiteral();
        $parts = $this->replace($literal);

        if ($parts === [$literal]) {
            return;
        }

        foreach ($parts as $part) {
            if ($part instanceof Node) {
                $text->insertBefore($part);
            } elseif ($part !== '') {
                $text->insertBefore(new Text($part));
            }
        }

        $text->detach();
    }
}
