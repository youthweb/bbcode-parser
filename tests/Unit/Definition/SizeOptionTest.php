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
use Youthweb\BBCodeParser\Definition\SizeOption;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(SizeOption::class)]
final class SizeOptionTest extends \PHPUnit\Framework\TestCase
{
    use MockerTrait;

    #[DataProvider('dataProvider')]
    public function testAsHtml(string $text, string|array $attribute, string $expected): void
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->createStub(\Youthweb\BBCodeParser\Config::class);

        $config->method('get')
            ->willReturnMap(
                [
                    ['callbacks.size_param.max_size', null, '150'],
                    ['callbacks.size_param.min_size', null, '75'],
                ],
            );

        $definition = new SizeOption($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public static function dataProvider(): \Iterator
    {
        yield [
            'some text',
            '100',
            '<span style="font-size:100%;">some text</span>',
        ];
        yield [
            'some text',
            ['100'],
            '<span style="font-size:100%;">some text</span>',
        ];
        yield [
            'some text',
            'NaN',
            '<span style="font-size:100%;">some text</span>',
        ];
        yield [
            'some text',
            '150',
            '<span style="font-size:150%;">some text</span>',
        ];
        yield [
            'some text',
            '500',
            '<span style="font-size:150%;">some text</span>',
        ];
        yield [
            'some text',
            '75',
            '<span style="font-size:75%;">some text</span>',
        ];
        yield [
            'some text',
            '5',
            '<span style="font-size:75%;">some text</span>',
        ];
        yield [
            '',
            '100',
            '',
        ];
    }
}
