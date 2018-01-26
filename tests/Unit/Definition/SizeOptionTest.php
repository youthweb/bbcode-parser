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
use Youthweb\BBCodeParser\Definition\SizeOption;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class SizeOptionTest extends \PHPUnit\Framework\TestCase
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
                        ['callbacks.size_param.max_size', null, '150'],
                        ['callbacks.size_param.min_size', null, '75'],
                    ]
                )
            );

        $definition = new SizeOption($config);

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
                '100',
                '<span style="font-size:100%;">some text</span>',
            ],
            [
                'some text',
                ['100'],
                '<span style="font-size:100%;">some text</span>',
            ],
            [
                'some text',
                'NaN',
                '<span style="font-size:100%;">some text</span>',
            ],
            [
                'some text',
                '150',
                '<span style="font-size:150%;">some text</span>',
            ],
            [
                'some text',
                '500',
                '<span style="font-size:150%;">some text</span>',
            ],
            [
                'some text',
                '75',
                '<span style="font-size:75%;">some text</span>',
            ],
            [
                'some text',
                '5',
                '<span style="font-size:75%;">some text</span>',
            ],
            [
                '',
                '100',
                '',
            ],
        ];
    }
}
