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

use Youthweb\BBCodeParser\Visitor\VisitorUrl;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(VisitorUrl::class)]
class VisitorUrlTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    public function testCreate(): void
    {
        $config = $this->getMockBuilder(\Youthweb\BBCodeParser\Config::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $visitor = new VisitorUrl();

        $config = new \Youthweb\BBCodeParser\Config();
        $visitor->setConfig($config);

        $this->assertInstanceOf(\Youthweb\BBCodeParser\Visitor\VisitorInterface::class, $visitor);
    }

    #[Test]
    public function testVisitDocumentElement(): void
    {
        $visitor = new VisitorUrl();

        $config = new \Youthweb\BBCodeParser\Config();
        $visitor->setConfig($config);

        $child = $this->getMockBuilder(\JBBCode\Node::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $child->expects($this->once())
            ->method('accept')
            ->with($visitor)
            ->willReturn(null);

        $element = $this->getMockBuilder(\JBBCode\DocumentElement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $element->method('getChildren')
            ->willReturn([$child]);

        $visitor->visitDocumentElement($element);
    }

    #[Test]
    public function testVisitElementNode(): void
    {
        $visitor = new VisitorUrl();

        $config = new \Youthweb\BBCodeParser\Config();
        $visitor->setConfig($config);

        $code_definition = $this->getMockBuilder(\JBBCode\CodeDefinition::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

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

        $element = $this->getMockBuilder(\JBBCode\ElementNode::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $element->method('getCodeDefinition')
            ->willReturn($code_definition);

        $element->method('getChildren')
            ->willReturn([$child]);

        $visitor->visitElementNode($element);
    }

    #[DataProvider('SmileyDataProvider')]
    public function testVisitTextNode(string $src, string $expected): void
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

        $visitor = new VisitorUrl();

        $config = new \Youthweb\BBCodeParser\Config();
        $visitor->setConfig($config);

        $visitor->visitTextNode($textnode);
    }

    /**
     * dataProvider
     */
    public static function SmileyDataProvider()
    {
        return [
            [
                'example.org',
                '<a target="_blank" href="http://example.org">http://example.org</a>',
            ],
            [
                'Hier ist meine Webseite: example.org',
                'Hier ist meine Webseite: <a target="_blank" href="http://example.org">http://example.org</a>',
            ],
        ];
    }
}
