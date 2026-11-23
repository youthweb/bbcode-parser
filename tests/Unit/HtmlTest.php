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

use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Html::class)]
final class HtmlTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('ImageDataProvider')]
    public function testImg(string $src, array $attr, string $expected): void
    {
        $this->assertSame($expected, Html::img($src, $attr));
    }

    public static function ImageDataProvider(): \Iterator
    {
        yield [
            'http://example.org/image.jpg',
            ['hidden' => false],
            '<img src="http://example.org/image.jpg" alt="image" />',
        ];
        yield [
            'example.org/image.jpg',
            ['hidden'],
            '<img hidden="hidden" src="/example.org/image.jpg" alt="image" />',
        ];
    }

    #[DataProvider('AnchorUrlDataProvider')]
    public function testAnchorCompletesUrl(string $href, string $expected): void
    {
        $this->assertSame('<a href="' . $expected . '">text</a>', Html::anchor($href, 'text'));
    }

    public static function AnchorUrlDataProvider(): \Iterator
    {
        yield 'userinfo, port, query and fragment' => ['http://user:pw@example.org:8080/p?q=1#frag', 'http://user:pw@example.org:8080/p?q=1#frag'];
        yield 'user without password' => ['http://user@example.org', 'http://user@example.org'];
        yield 'port and trailing slash' => ['http://example.org:8080/', 'http://example.org:8080'];
        yield 'fragment' => ['https://example.org/page#section', 'https://example.org/page#section'];
        yield 'query is escaped' => ['http://example.org/?a=b&c=d', 'http://example.org/?a=b&amp;c=d'];
        yield 'empty query and fragment are dropped' => ['http://example.org/?#', 'http://example.org'];
        yield 'ipv6 host with port' => ['http://[::1]:8080/x', 'http://[::1]:8080/x'];
        yield 'path 0 is kept' => ['http://example.org/0', 'http://example.org/0'];
        yield 'percent encoding is kept' => ['http://example.org/a%20b', 'http://example.org/a%20b'];
        yield 'scheme and host case is kept' => ['HTTP://EXAMPLE.ORG/A', 'HTTP://EXAMPLE.ORG/A'];
        yield 'scheme-relative url loses its slashes' => ['//example.org/x', 'example.org/x'];
        yield 'absolute path' => ['/relative/path', '/relative/path'];
        yield 'mailto gets slashes' => ['mailto:a@b.de', 'mailto:///a@b.de'];
        yield 'unparsable url gives an empty href' => ['http:///x', ''];
    }

    #[DataProvider('ImageUrlWithoutSchemeDataProvider')]
    public function testImgCompletesUrlWithoutScheme(string $src, string $expected): void
    {
        $this->assertStringStartsWith('<img src="' . $expected . '" ', Html::img($src));
    }

    public static function ImageUrlWithoutSchemeDataProvider(): \Iterator
    {
        yield 'scheme-relative url loses its slashes' => ['//example.org/x', 'example.org/x'];
        yield 'absolute path' => ['/relative/path', '/relative/path'];
        yield 'relative path gets a slash' => ['relative/path.png', '/relative/path.png'];
        yield 'mailto gets slashes' => ['mailto:a@b.de', 'mailto:///a@b.de'];
    }

    #[DataProvider('WellFormedUrlDataProvider')]
    public function testBuildUrlKeepsWellFormedUrl(string $url): void
    {
        $this->assertSame($url, $this->buildUrl($url));
    }

    public static function WellFormedUrlDataProvider(): \Iterator
    {
        yield 'host only' => ['http://example.org'];
        yield 'trailing slash' => ['http://example.org/'];
        yield 'path' => ['https://example.org/path/to/page.html'];
        yield 'query' => ['http://example.org/?a=1&b=2'];
        yield 'fragment' => ['http://example.org/#section'];
        yield 'path, query and fragment' => ['https://example.org/p?q=1#f'];
        yield 'port' => ['http://example.org:8080/'];
        yield 'user without password' => ['http://user@example.org/'];
        yield 'user with password' => ['http://user:pw@example.org/'];
        yield 'user, password and port' => ['http://user:pw@example.org:8080/x'];
        yield 'ipv6' => ['http://[::1]/'];
        yield 'ipv6 with port' => ['http://[2001:db8::1]:8080/x'];
        yield 'ftp' => ['ftp://ftp.example.org/file.txt'];
    }

    #[DataProvider('NormalizedUrlDataProvider')]
    public function testBuildUrlNormalizesUrl(string $url, string $expected): void
    {
        $this->assertSame($expected, $this->buildUrl($url));
    }

    public static function NormalizedUrlDataProvider(): \Iterator
    {
        yield 'path without scheme and host gets a leading slash' => ['example.org/image.jpg', '/example.org/image.jpg'];
        yield 'relative path gets a leading slash' => ['relative/path.png', '/relative/path.png'];
        yield 'scheme-relative url loses its slashes' => ['//example.org/x', 'example.org/x'];
        yield 'empty query is dropped' => ['http://example.org/?', 'http://example.org/'];
        yield 'empty fragment is dropped' => ['http://example.org/#', 'http://example.org/'];
        yield 'query 0 is dropped' => ['http://example.org/?0', 'http://example.org/'];
        yield 'fragment 0 is dropped' => ['http://example.org/#0', 'http://example.org/'];
        yield 'host 0 is dropped' => ['http://0/x', 'http:///x'];
        yield 'mailto gets slashes' => ['mailto:a@b.de', 'mailto:///a@b.de'];
        yield 'javascript gets slashes' => ['javascript:alert(1)', 'javascript:///alert(1)'];
        yield 'unparsable url gives an empty string' => ['http:///x', ''];
    }

    public function testBuildUrlGivesEmptyStringForNullWithoutDeprecation(): void
    {
        $deprecations = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecations): bool {
            $deprecations[] = $errstr;

            return true;
        }, E_DEPRECATED);

        try {
            $result = (new \ReflectionMethod(Html::class, 'buildUrl'))->invoke(null, null);
        } finally {
            restore_error_handler();
        }

        $this->assertSame('', $result);
        $this->assertSame([], $deprecations);
    }

    private function buildUrl(string $url): string
    {
        return (new \ReflectionMethod(Html::class, 'buildUrl'))->invoke(null, $url);
    }

    #[DataProvider('ListDataProvider')]
    public function testList(array $src, array $attr, string $expected): void
    {
        $this->assertSame($expected, Html::ul($src, $attr));
    }

    public static function ListDataProvider(): \Iterator
    {
        yield [
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
        ];
    }

    /**
     * @param array<string, mixed> $config
     */
    #[DataProvider('shortenUrlTextProvider')]
    public function testShortenUrlText(string $url, array $config, string $expected): void
    {
        $object = new Config();
        $object->mergeRecursive($config);

        $this->assertSame($expected, Html::shortenUrlText($url, $object));
    }

    public static function shortenUrlTextProvider(): \Iterator
    {
        $long = 'http://www.example.com/irgend/eine/lange/url.html';

        yield 'off by default' => [$long, [], $long];
        yield 'short enough, only the scheme goes' => [
            'http://a.de/x',
            ['callbacks' => ['url_content' => ['short_url' => true]]],
            'a.de/x',
        ];
        yield 'cut at the end below 30' => [
            $long,
            ['callbacks' => ['url_content' => ['short_url' => true, 'short_url_length' => 25]]],
            "www.example.com/irgend/\u{2026}",
        ];
        yield 'cut in the middle from 30' => [
            $long,
            ['callbacks' => ['url_content' => ['short_url' => true, 'short_url_length' => 40]]],
            "www.example.com/irgend/eine/\u{2026}e/url.html",
        ];
        yield 'the minimum length is 20' => [
            $long,
            ['callbacks' => ['url_content' => ['short_url' => true, 'short_url_length' => 5]]],
            "www.example.com/ir\u{2026}",
        ];
    }
}
