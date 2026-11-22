<?php

/*
 * A BBCode-to-HTML parser for youthweb.net
 * Copyright (C) 2016-2021  Youthweb e.V. <info@youthweb.net>

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Youthweb\BBCodeParser\Tests\Unit\Visitor;

use Youthweb\BBCodeParser\Visitor\VisitorSmiley;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(VisitorSmiley::class)]
class VisitorSmileyTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    public function testCreate(): void
    {
        $config = $this->createStub(\Youthweb\BBCodeParser\Config::class);

        $visitor = new VisitorSmiley();

        $visitor->setConfig($config);

        $this->assertInstanceOf(\Youthweb\BBCodeParser\Visitor\VisitorInterface::class, $visitor);
    }

    #[Test]
    public function testVisitDocumentElement(): void
    {
        $visitor = new VisitorSmiley();

        $child = $this->getMockBuilder(\JBBCode\Node::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $child->expects($this->once())
            ->method('accept')
            ->with($visitor)
            ->willReturn(null);

        $element = $this->createStub(\JBBCode\DocumentElement::class);

        $element->method('getChildren')
            ->willReturn([$child]);

        $visitor->visitDocumentElement($element);
    }

    #[Test]
    public function testVisitElementNode(): void
    {
        $visitor = new VisitorSmiley();

        $code_definition = $this->createStub(\JBBCode\CodeDefinition::class);

        $code_definition->method('parseContent')
            ->willReturn(true);

        $child = $this->getMockBuilder(\JBBCode\Node::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $child->expects($this->once())
            ->method('accept')
            ->with($visitor)
            ->willReturn(null);

        $element = $this->createStub(\JBBCode\ElementNode::class);

        $element->method('getCodeDefinition')
            ->willReturn($code_definition);

        $element->method('getChildren')
            ->willReturn([$child]);

        $visitor->visitElementNode($element);
    }

    #[DataProvider('SmileyDataProvider')]
    public function testvisitTextNode(string $src, string $expected): void
    {
        $textnode = $this->getMockBuilder(\JBBCode\TextNode::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $textnode->method('getValue')
            ->willReturn($src);

        $textnode->expects($this->once())
            ->method('setValue')
            ->with($expected)
            ->willReturn(null);

        $visitor = new VisitorSmiley();

        $visitor->visitTextNode($textnode);
    }

    /**
     * dataProvider
     */
    public static function SmileyDataProvider()
    {
        return [
            [
                'Hi :-)',
                'Hi {{{lt}}}img src={{{quot}}}https://youthweb.net/vendor/smilies/smile0001.gif{{{quot}}} alt={{{quot}}}:-){{{quot}}} title={{{quot}}}:-){{{quot}}} /{{{gt}}}',
            ],
            [
                'Hey :super: Das war sehr gut.',
                'Hey {{{lt}}}img src={{{quot}}}https://youthweb.net/vendor/smilies/489.gif{{{quot}}} alt={{{quot}}}:super:{{{quot}}} title={{{quot}}}:super:{{{quot}}} /{{{gt}}} Das war sehr gut.',
            ],
        ];
    }
}
