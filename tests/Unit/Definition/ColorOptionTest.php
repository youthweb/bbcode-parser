<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
