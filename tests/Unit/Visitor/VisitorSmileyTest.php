<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
