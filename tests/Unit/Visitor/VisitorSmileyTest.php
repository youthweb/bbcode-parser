<?php
/*
 * A BBCode-to-HTML parser for youthweb.net
 * Copyright (C) 2016-2019  Youthweb e.V. <info@youthweb.net>

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

class VisitorSmileyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test create
     */
    public function testCreate()
    {
        $config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $visitor = new VisitorSmiley();

        $visitor->setConfig($config);

        $this->assertInstanceOf('Youthweb\BBCodeParser\Visitor\VisitorInterface', $visitor);
    }

    /**
     * @test visitDocumentElement
     */
    public function testVisitDocumentElement()
    {
        $visitor = new VisitorSmiley();

        $child = $this->getMockBuilder('JBBCode\Node')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $child->expects($this->once())
            ->method('accept')
            ->with($visitor)
            ->willReturn(null);

        $element = $this->getMockBuilder('JBBCode\DocumentElement')
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
    public function testVisitElementNode()
    {
        $visitor = new VisitorSmiley();

        $code_definition = $this->getMockBuilder('JBBCode\CodeDefinition')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $code_definition->method('parseContent')
            ->willReturn(true);

        $child = $this->getMockBuilder('JBBCode\Node')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $child->expects($this->once())
            ->method('accept')
            ->with($visitor)
            ->willReturn(null);

        $element = $this->getMockBuilder('JBBCode\ElementNode')
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
     *
     * @param mixed $src
     * @param mixed $expected
     */
    public function testvisitTextNode($src, $expected)
    {
        $textnode = $this->getMockBuilder('JBBCode\TextNode')
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
                'Hi <img src="https://youthweb.net/vendor/smilies/smile0001.gif" alt=":-)" title=":-)" />',
            ],
            [
                'Hey :super: Das war sehr gut.',
                'Hey <img src="https://youthweb.net/vendor/smilies/489.gif" alt=":super:" title=":super:" /> Das war sehr gut.',
            ],
        ];
    }
}
