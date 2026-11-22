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

/**
 * @covers \Youthweb\BBCodeParser\Visitor\VisitorSmiley
 */
class VisitorSmileyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test create
     */
    public function testCreate(): void
    {
        $config = $this->getMockBuilder(\Youthweb\BBCodeParser\Config::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $visitor = new VisitorSmiley();

        $visitor->setConfig($config);

        $this->assertInstanceOf(\Youthweb\BBCodeParser\Visitor\VisitorInterface::class, $visitor);
    }

    /**
     * @test visitDocumentElement
     */
    public function testVisitDocumentElement(): void
    {
        $visitor = new VisitorSmiley();

        $child = $this->getMockBuilder(\JBBCode\Node::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $child->expects($this->once())
            ->method('accept')
            ->with($visitor)
            ->willReturn(null);

        $element = $this->getMockBuilder(\JBBCode\DocumentElement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $element->method('getChildren')
            ->willReturn([$child]);

        $visitor->visitDocumentElement($element);
    }

    /**
     * @test visitElementNode
     */
    public function testVisitElementNode(): void
    {
        $visitor = new VisitorSmiley();

        $code_definition = $this->getMockBuilder(\JBBCode\CodeDefinition::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $code_definition->method('parseContent')
            ->willReturn(true);

        $child = $this->getMockBuilder(\JBBCode\Node::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $child->expects($this->once())
            ->method('accept')
            ->with($visitor)
            ->willReturn(null);

        $element = $this->getMockBuilder(\JBBCode\ElementNode::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $element->method('getCodeDefinition')
            ->willReturn($code_definition);

        $element->method('getChildren')
            ->willReturn([$child]);

        $visitor->visitElementNode($element);
    }

    /**
     * @dataProvider SmileyDataProvider
     */
    public function testvisitTextNode(string $src, string $expected): void
    {
        $textnode = $this->getMockBuilder(\JBBCode\TextNode::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
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
    public function SmileyDataProvider()
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
