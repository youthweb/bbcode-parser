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
use Youthweb\BBCodeParser\Definition\ListOption;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ListOptionTest extends \PHPUnit\Framework\TestCase
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

        $definition = new ListOption();

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public function dataProvider()
    {
        return [
            [
                '[*]Hello World!
[*]foobar',
                null,
                '<!-- no_p --><ul type="disc">' . PHP_EOL .
                "\t" . '<li>Hello World!</li>' . PHP_EOL .
                "\t" . '<li>foobar</li>' . PHP_EOL .
                '</ul>' . PHP_EOL .
                '<!-- no_p -->',
            ],
            [
                '[*]Hello World!
[*]foobar',
                'a',
                '<!-- no_p --><ol type="a">' . PHP_EOL .
                "\t" . '<li>Hello World!</li>' . PHP_EOL .
                "\t" . '<li>foobar</li>' . PHP_EOL .
                '</ol>' . PHP_EOL .
                '<!-- no_p -->',
            ],
            [
                '[*]Hello World!
[*]foobar',
                ['a'],
                '<!-- no_p --><ol type="a">' . PHP_EOL .
                "\t" . '<li>Hello World!</li>' . PHP_EOL .
                "\t" . '<li>foobar</li>' . PHP_EOL .
                '</ol>' . PHP_EOL .
                '<!-- no_p -->',
            ],
            [
                '[*]Hello World!
[*]foobar',
                'A',
                '<!-- no_p --><ol type="A">' . PHP_EOL .
                "\t" . '<li>Hello World!</li>' . PHP_EOL .
                "\t" . '<li>foobar</li>' . PHP_EOL .
                '</ol>' . PHP_EOL .
                '<!-- no_p -->',
            ],
            [
                '[*]Hello World!
[*]foobar',
                'i',
                '<!-- no_p --><ol type="i">' . PHP_EOL .
                "\t" . '<li>Hello World!</li>' . PHP_EOL .
                "\t" . '<li>foobar</li>' . PHP_EOL .
                '</ol>' . PHP_EOL .
                '<!-- no_p -->',
            ],
            [
                '[*]Hello World!
[*]foobar',
                'I',
                '<!-- no_p --><ol type="I">' . PHP_EOL .
                "\t" . '<li>Hello World!</li>' . PHP_EOL .
                "\t" . '<li>foobar</li>' . PHP_EOL .
                '</ol>' . PHP_EOL .
                '<!-- no_p -->',
            ],
            [
                '[*]Hello World!
[*]foobar',
                '5',
                '<!-- no_p --><ol start="5">' . PHP_EOL .
                "\t" . '<li>Hello World!</li>' . PHP_EOL .
                "\t" . '<li>foobar</li>' . PHP_EOL .
                '</ol>' . PHP_EOL .
                '<!-- no_p -->',
            ],
        ];
    }
}
