<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image as MarkdownImage;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use Youthweb\BBCodeParser\Extension\Node\BBList;
use Youthweb\BBCodeParser\Extension\Node\BBListItem;
use Youthweb\BBCodeParser\Extension\Node\Email;
use Youthweb\BBCodeParser\Extension\Node\Image;

/**
 * Turns the nodes of the Markdown parser into the nodes of the BBCode engine, so that one renderer serves both inputs:
 * lists become `BBList`, fenced code becomes code, an image becomes the image of `[img]` and a `mailto:` link
 * becomes the email of `[email]`.
 *
 * The alt text and the title of an image are dropped, like `[img]` has none. The code of Markdown ends with a
 * line break, the code of `[code]` does not: the last line break is cut, so that both give the same HTML.
 *
 * @experimental
 * @internal
 */
final readonly class MarkdownTreeListener
{
    public function __invoke(DocumentParsedEvent $event): void
    {
        $lists = [];
        $codes = [];
        $images = [];
        $indented = [];
        $mails = [];

        foreach ($event->getDocument()->iterator() as $node) {
            if ($node instanceof ListBlock) {
                $lists[] = $node;
            } elseif ($node instanceof FencedCode) {
                $codes[] = $node;
            } elseif ($node instanceof IndentedCode) {
                $indented[] = $node;
            } elseif ($node instanceof MarkdownImage) {
                $images[] = $node;
            } elseif ($node instanceof Link && stripos($node->getUrl(), 'mailto:') === 0) {
                $mails[] = $node;
            }
        }

        foreach ($lists as $list) {
            $this->convertList($list);
        }

        foreach ($codes as $code) {
            $block = new IndentedCode();
            $block->setLiteral($this->withoutLastLineBreak($code->getLiteral()));
            $code->replaceWith($block);
        }

        foreach ($indented as $code) {
            $code->setLiteral($this->withoutLastLineBreak($code->getLiteral()));
        }

        foreach ($images as $image) {
            $image->replaceWith(new Image($image->getUrl()));
        }

        foreach ($mails as $link) {
            $mail = new Email(substr($link->getUrl(), strlen('mailto:')));

            foreach ($link->children() as $child) {
                $mail->appendChild($child);
            }

            $link->replaceWith($mail);
        }
    }

    private function convertList(ListBlock $list): void
    {
        $data = $list->getListData();
        $option = $data->type === ListBlock::TYPE_ORDERED ? (string) ($data->start ?? 1) : null;

        $converted = new BBList($option);

        foreach ($list->children() as $item) {
            $convertedItem = new BBListItem();

            foreach ($item->children() as $child) {
                $convertedItem->appendChild($child);
            }

            $converted->appendChild($convertedItem);
        }

        $list->replaceWith($converted);
    }

    private function withoutLastLineBreak(string $code): string
    {
        return str_ends_with($code, "\n") ? substr($code, 0, -1) : $code;
    }
}
