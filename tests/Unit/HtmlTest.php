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

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Html::class)]
class HtmlTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('ImageDataProvider')]
    public function testImg(string $src, array $attr, string $expected): void
    {
        $this->assertSame($expected, Html::img($src, $attr));
    }

    public static function ImageDataProvider()
    {
        return [
            [
                'http://example.org/image.jpg',
                ['hidden' => false],
                '<img src="http://example.org/image.jpg" alt="image" />',
            ],
            [
                'example.org/image.jpg',
                ['hidden'],
                '<img hidden="hidden" src="/example.org/image.jpg" alt="image" />',
            ],
        ];
    }

    #[DataProvider('ListDataProvider')]
    public function testList(array $src, array $attr, string $expected): void
    {
        $this->assertSame($expected, Html::ul($src, $attr));
    }

    public static function ListDataProvider()
    {
        return [
            [
                [
                    'item one',
                    [
                        'item 1.1',
                        'item 1.2',
                    ],
                    'item 2',
                ],
                [],
                '<ul>' . PHP_EOL
                . "\t" . '<li>item one</li>' . PHP_EOL
                . "\t" . '<li>' . PHP_EOL
                . "\t\t" . '<ul>' . PHP_EOL
                . "\t\t\t" . '<li>item 1.1</li>' . PHP_EOL
                . "\t\t\t" . '<li>item 1.2</li>' . PHP_EOL
                . "\t\t" . '</ul>' . PHP_EOL
                . "\t" . '</li>' . PHP_EOL
                . "\t" . '<li>item 2</li>' . PHP_EOL
                . '</ul>' . PHP_EOL,
            ],
        ];
    }
}
