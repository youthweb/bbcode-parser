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

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\ColorOption;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ColorOptionTest extends \PHPUnit\Framework\TestCase
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
                        ['callbacks.color_param.allowed_colors', null, ['Red', 'Yellow']],
                        ['callbacks.color_param.default_color', null, 'Red'],
                    ]
                )
            );

        $definition = new ColorOption($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public function dataProvider()
    {
        return [
            [
                'some text',
                'red',
                '<span style="color:Red;">some text</span>',
            ],
            [
                'some text',
                'yellow',
                '<span style="color:Yellow;">some text</span>',
            ],
            [
                'some text',
                ['yellow'],
                '<span style="color:Yellow;">some text</span>',
            ],
            [
                'some text',
                'unknown',
                '<span style="color:Red;">some text</span>',
            ],
            [
                'some text',
                '#112233',
                '<span style="color:#112233;">some text</span>',
            ],
            [
                '',
                null,
                '',
            ],
        ];
    }
}
