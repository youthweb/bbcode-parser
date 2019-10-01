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

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\V;
use Youthweb\BBCodeParser\Filter\FilterException;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class VTest extends \PHPUnit\Framework\TestCase
{
    use MockerTrait;

    /**
     * @dataProvider dataProvider
     *
     * @param mixed $text
     * @param mixed $attribute
     * @param mixed $expected
     */
    public function testAsHtml($text, $attribute, $expected)
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
            ->setMethods(['get'])
            ->getMock();

        $config->expects($this->any())
            ->method('get')
            ->will(
                $this->returnValueMap(
                    [
                        ['callbacks.url_content.target', null, '_blank'],
                        ['filter.video', null, false],
                    ]
                )
            );

        $definition = new V($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public function dataProvider()
    {
        return [
            [
                'http://example.org/video.mp4',
                null,
                '<a target="_blank" href="http://example.org/video.mp4">http://example.org/video.mp4</a>',
            ],
            [
                'example.org/video.mp4',
                null,
                '<a target="_blank" href="http://example.org/video.mp4">example.org/video.mp4</a>',
            ],
            [
                'invalid url',
                null,
                'invalid url',
            ],
            [
                '',
                null,
                '',
            ],
        ];
    }

    /**
     * @test
     */
    public function testAsHtmlWithFilter()
    {
        $text = 'http://example.org/video.mp4';
        $attribute = null;
        $expected = 'some value';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $filter = $this->getMockBuilder('Youthweb\BBCodeParser\Filter\FilterInterface')
            ->setMethods(['setConfig', 'execute'])
            ->getMock();

        $filter->expects($this->any())
            ->method('execute')
            ->willReturn($expected);

        $config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
            ->setMethods(['get'])
            ->getMock();

        $config->expects($this->any())
            ->method('get')
            ->will(
                $this->returnValueMap(
                    [
                        ['callbacks.url_content.target', null, '_blank'],
                        ['filter.video', null, $filter],
                    ]
                )
            );

        $definition = new V($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * @test
     */
    public function testAsHtmlWithFilterException()
    {
        $text = 'http://example.org/video.mp4';
        $attribute = null;
        $expected = '<a target="_blank" href="http://example.org/video.mp4">http://example.org/video.mp4</a>';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $filter = $this->getMockBuilder('Youthweb\BBCodeParser\Filter\FilterInterface')
            ->setMethods(['setConfig', 'execute'])
            ->getMock();

        $filter->expects($this->any())
            ->method('execute')
            ->willThrowException(new FilterException);

        $config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
            ->setMethods(['get'])
            ->getMock();

        $config->expects($this->any())
            ->method('get')
            ->will(
                $this->returnValueMap(
                    [
                        ['callbacks.url_content.target', null, '_blank'],
                        ['filter.video', null, $filter],
                    ]
                )
            );

        $definition = new V($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }
}
