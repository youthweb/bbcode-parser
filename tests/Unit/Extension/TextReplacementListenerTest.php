<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Unit\Extension;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Extension\Node\Email;
use Youthweb\BBCodeParser\Extension\Node\TrustedHtml;
use Youthweb\BBCodeParser\Extension\TextReplacementListener;

#[CoversClass(TextReplacementListener::class)]
final class TextReplacementListenerTest extends TestCase
{
    public function testAPartIsReplacedByANode(): void
    {
        $paragraph = $this->listen(new Text('a X b'));

        $this->assertSame(['a ', TrustedHtml::class, ' b'], $this->describe($paragraph));
    }

    public function testATextWithoutAChangeStaysTheSameNode(): void
    {
        $text = new Text('a b');
        $paragraph = $this->listen($text);

        $this->assertSame($text, $paragraph->firstChild());
    }

    public function testEmptyPartsAreDropped(): void
    {
        $paragraph = $this->listen(new Text('X'));

        $this->assertSame([TrustedHtml::class], $this->describe($paragraph));
    }

    public function testATextCanBeRemoved(): void
    {
        $paragraph = $this->listen(new Text('DROP'));

        $this->assertSame([], $this->describe($paragraph));
    }

    public function testRawTextIsLeftAlone(): void
    {
        $text = new Text('a X b');
        $text->data->set(TextReplacementListener::RAW, true);

        $this->assertSame(['a X b'], $this->describe($this->listen($text)));
    }

    public function testTextInsideOfALinkIsLeftAlone(): void
    {
        $link = new Link('https://example.org');
        $link->appendChild(new Text('a X b'));

        $paragraph = $this->listen($link);

        $this->assertSame(['a X b'], $this->describe($link));
        $this->assertSame($link, $paragraph->firstChild());
    }

    public function testTextInsideOfAnEmailIsLeftAlone(): void
    {
        $email = new Email('a@example.org');
        $email->appendChild(new Text('a X b'));

        $this->listen($email);

        $this->assertSame(['a X b'], $this->describe($email));
    }

    public function testTextNextToALinkIsReplaced(): void
    {
        $link = new Link('https://example.org');
        $link->appendChild(new Text('X'));

        $document = new Document();
        $paragraph = new Paragraph();
        $document->appendChild($paragraph);
        $paragraph->appendChild($link);
        $paragraph->appendChild(new Text(' X'));

        $this->listener()(new DocumentParsedEvent($document));

        $this->assertSame([Link::class, ' ', TrustedHtml::class], $this->describe($paragraph));
    }

    private function listen(Node $child): Paragraph
    {
        $document = new Document();
        $paragraph = new Paragraph();
        $document->appendChild($paragraph);
        $paragraph->appendChild($child);

        $this->listener()(new DocumentParsedEvent($document));

        return $paragraph;
    }

    /**
     * @return list<string> the text of a text node, the class of any other node
     */
    private function describe(Node $parent): array
    {
        $result = [];

        foreach ($parent->children() as $child) {
            $result[] = $child instanceof Text ? $child->getLiteral() : $child::class;
        }

        return $result;
    }

    private function listener(): TextReplacementListener
    {
        return new class extends TextReplacementListener {
            protected function replace(string $text): array
            {
                if ($text === 'DROP') {
                    return [];
                }

                $parts = explode('X', $text);

                if (count($parts) === 1) {
                    return [$text];
                }

                $result = [];

                foreach ($parts as $i => $part) {
                    if ($i > 0) {
                        $result[] = new TrustedHtml('<x>');
                    }

                    $result[] = $part;
                }

                return $result;
            }
        };
    }
}
