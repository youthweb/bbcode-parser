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
use Youthweb\BBCodeParser\Definition\Url;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Url::class)]
final class UrlTest extends \PHPUnit\Framework\TestCase
{
    use MockerTrait;

    #[DataProvider('dataProvider')]
    public function testAsHtml(string $text, null $attribute, string $expected): void
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.short_url', null, false],
                ['callbacks.url_content.short_url_length', null, 55],
                ['callbacks.url_content.target', null, '_blank'],
            ],
        );

        $definition = new Url($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithShortenLongUrl(): void
    {
        $text = 'http://example.org/this/is/a/very/long/url.with?query=params';
        $attribute = null;
        $expected = '<a target="_blank" href="http://example.org/this/is/a/very/long/url.with?query=params">example.org/this/i…ery=params</a>';

        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.short_url', null, true],
                ['callbacks.url_content.short_url_length', null, 30],
                ['callbacks.url_content.target', null, '_blank'],
            ],
        );

        $definition = new Url($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithShortenShortUrl(): void
    {
        $text = 'http://example.org/this/is/a/very/long/url.with?query=params';
        $attribute = null;
        $expected = '<a target="_blank" href="http://example.org/this/is/a/very/long/url.with?query=params">example.org/this/i…</a>';

        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.short_url', null, true],
                ['callbacks.url_content.short_url_length', null, 20],
                ['callbacks.url_content.target', null, '_blank'],
            ],
        );

        $definition = new Url($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithoutTarget(): void
    {
        $text = 'http://example.org/this/is/a/very/long/url.with?query=params';
        $attribute = null;
        $expected = '<a href="http://example.org/this/is/a/very/long/url.with?query=params">example.org/this/is/a/very/long/url.with?query=params</a>';

        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.short_url', null, true],
                ['callbacks.url_content.short_url_length', null, 55],
                ['callbacks.url_content.target', null, null],
            ],
        );

        $definition = new Url($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithTargetOnYouthwebUrl(): void
    {
        $text = 'http://youthweb.net/this/is/a/very/long/url.with?query=params';
        $attribute = null;
        $expected = '<a href="http://youthweb.net/this/is/a/very/long/url.with?query=params">youthweb.net/this/is/a/very/long/url.with?query=params</a>';

        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.short_url', null, true],
                ['callbacks.url_content.short_url_length', null, 55],
                ['callbacks.url_content.target', null, '_blank'],
            ],
        );

        $definition = new Url($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithTargetOnYouthwebUrlWithoutHttp(): void
    {
        $text = 'ftp://youthweb.net/this/is/a/very/long/url.with?query=params';
        $attribute = null;
        $expected = '<a target="_blank" href="ftp://youthweb.net/this/is/a/very/long/url.with?query=params">youthweb.net/this/is/a/very/long/url.with?query=params</a>';

        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.short_url', null, true],
                ['callbacks.url_content.short_url_length', null, 55],
                ['callbacks.url_content.target', null, '_blank'],
            ],
        );

        $definition = new Url($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public static function dataProvider(): \Iterator
    {
        yield [
            'http://example.org/foo?query=string&foo=bar',
            null,
            '<a target="_blank" href="http://example.org/foo?query=string&amp;foo=bar">http://example.org/foo?query=string&foo=bar</a>',
        ];
        yield [
            'http://example.org',
            null,
            '<a target="_blank" href="http://example.org">http://example.org</a>',
        ];
        yield [
            'invalid url',
            null,
            'invalid url',
        ];
        yield [
            '',
            null,
            '',
        ];
    }
}
