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

    #[DataProvider('AnchorUrlDataProvider')]
    public function testAnchorCompletesUrl(string $href, string $expected): void
    {
        $this->assertSame('<a href="' . $expected . '">text</a>', Html::anchor($href, 'text'));
    }

    public static function AnchorUrlDataProvider()
    {
        return [
            'userinfo, port, query and fragment' => ['http://user:pw@example.org:8080/p?q=1#frag', 'http://user:pw@example.org:8080/p?q=1#frag'],
            'user without password' => ['http://user@example.org', 'http://user@example.org'],
            'port and trailing slash' => ['http://example.org:8080/', 'http://example.org:8080'],
            'fragment' => ['https://example.org/page#section', 'https://example.org/page#section'],
            'query is escaped' => ['http://example.org/?a=b&c=d', 'http://example.org/?a=b&amp;c=d'],
            'empty query and fragment are dropped' => ['http://example.org/?#', 'http://example.org'],
            'ipv6 host with port' => ['http://[::1]:8080/x', 'http://[::1]:8080/x'],
            'path 0 is kept' => ['http://example.org/0', 'http://example.org/0'],
            'percent encoding is kept' => ['http://example.org/a%20b', 'http://example.org/a%20b'],
            'scheme and host case is kept' => ['HTTP://EXAMPLE.ORG/A', 'HTTP://EXAMPLE.ORG/A'],
            'scheme-relative url loses its slashes' => ['//example.org/x', 'example.org/x'],
            'absolute path' => ['/relative/path', '/relative/path'],
            'mailto gets slashes' => ['mailto:a@b.de', 'mailto:///a@b.de'],
            'unparsable url gives an empty href' => ['http:///x', ''],
        ];
    }

    #[DataProvider('ImageUrlWithoutSchemeDataProvider')]
    public function testImgCompletesUrlWithoutScheme(string $src, string $expected): void
    {
        $this->assertStringStartsWith('<img src="' . $expected . '" ', Html::img($src));
    }

    public static function ImageUrlWithoutSchemeDataProvider()
    {
        return [
            'scheme-relative url loses its slashes' => ['//example.org/x', 'example.org/x'],
            'absolute path' => ['/relative/path', '/relative/path'],
            'relative path gets a slash' => ['relative/path.png', '/relative/path.png'],
            'mailto gets slashes' => ['mailto:a@b.de', 'mailto:///a@b.de'],
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
