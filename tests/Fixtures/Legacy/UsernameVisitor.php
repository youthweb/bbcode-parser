<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures\Legacy;

use JBBCode\DocumentElement;
use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Visitor\VisitorInterface;

/**
 * Models a username visitor of a project for 1.x, the counterpart of `UsernameExtension`: it writes the link into the text.
 */
final class UsernameVisitor implements VisitorInterface
{
    private const USERS = ['alice' => 1, 'bob_2' => 2];

    public function setConfig(Config $config): self
    {
        return $this;
    }

    public function visitDocumentElement(DocumentElement $document_element): void
    {
        foreach ($document_element->getChildren() as $child) {
            $child->accept($this);
        }
    }

    public function visitTextNode(TextNode $text_node): void
    {
        $text = (string) preg_replace_callback('~(^|\s)@([a-z0-9_]{3,15})~i', static function (array $m): string {
            if (! isset(self::USERS[$m[2]])) {
                return $m[0];
            }

            return $m[1] . '{{{lt}}}a href={{{quot}}}https://example.org/users/' . self::USERS[$m[2]] . '{{{quot}}}{{{gt}}}@' . $m[2] . '{{{lt}}}/a{{{gt}}}';
        }, $text_node->getValue());

        $text_node->setValue($text);
    }

    public function visitElementNode(ElementNode $element_node): void
    {
        if ($element_node->getCodeDefinition()->parseContent()) {
            foreach ($element_node->getChildren() as $child) {
                $child->accept($this);
            }
        }
    }
}
