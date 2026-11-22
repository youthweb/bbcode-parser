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
use Youthweb\BBCodeParser\Definition\Image;
use Youthweb\BBCodeParser\Filter\FilterException;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Image::class)]
final class ImageTest extends \PHPUnit\Framework\TestCase
{
    use MockerTrait;

    #[DataProvider('dataProvider')]
    public function testAsHtml(string $text, null $attribute, string $expected): void
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->buildConfigStub(
            [
                ['callbacks.image.force_check', null, false],
            ],
        );

        $definition = new Image($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithFilter(): void
    {
        $text = 'http://example.org/this/is/a/very/long/url.with?query=params';
        $attribute = null;
        $expected = 'some value';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $filter = $this->createStub(\Youthweb\BBCodeParser\Filter\FilterInterface::class);

        $filter->method('execute')
            ->willReturn($expected);

        $config = $this->buildConfigStub(
            [
                ['callbacks.image.force_check', null, false],
                ['filter.image', null, $filter],
            ],
        );

        $definition = new Image($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithFilterException(): void
    {
        $text = 'http://example.org/image.jpg';
        $attribute = null;
        $expected = '<a target="_blank" href="http://example.org/image.jpg"><img class="img-responsive" border="0" src="http://example.org/image.jpg" alt="image" /></a>';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $filter = $this->createStub(\Youthweb\BBCodeParser\Filter\FilterInterface::class);

        $filter->method('execute')
            ->willThrowException(new FilterException());

        $config = $this->buildConfigStub(
            [
                ['callbacks.image.force_check', null, false],
                ['filter.image', null, $filter],
            ],
        );

        $definition = new Image($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithUrlParent(): void
    {
        $text = 'http://example.org/image.jpg';
        $attribute = null;
        $expected = '<img class="img-responsive" border="0" src="http://example.org/image.jpg" alt="image" />';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $elementNode->method('closestParentOfType')
            ->willReturn(true);

        $config = $this->buildConfigStub(
            [
                ['callbacks.image.force_check', null, false],
            ],
        );

        $definition = new Image($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithForceCheck(): void
    {
        $text = 'http://example.org/not-an-image.txt';
        $attribute = null;
        $expected = 'http://example.org/not-an-image.txt';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $elementNode->method('closestParentOfType')
            ->willReturn(true);

        $validation = $this->createStub(\Youthweb\BBCodeParser\ValidationInterface::class);

        $validation->method('isValidImageUrl')
            ->willReturn(false);

        $validation->method('isValidUrl')
            ->willReturn(true);

        $config = $this->createStub(\Youthweb\BBCodeParser\Config::class);

        $config->method('getValidation')
            ->willReturn($validation);

        $config->method('get')
            ->willReturnMap(
                [
                    ['callbacks.image.force_check', null, true],
                ],
            );

        $definition = new Image($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithForceCheckAndAnchor(): void
    {
        $text = 'http://example.org/not-an-image.txt';
        $attribute = null;
        $expected = '<a href="http://example.org/not-an-image.txt">http://example.org/not-an-image.txt</a>';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $elementNode->method('closestParentOfType')
            ->willReturn(false);

        $validation = $this->createStub(\Youthweb\BBCodeParser\ValidationInterface::class);

        $validation->method('isValidImageUrl')
            ->willReturn(false);

        $validation->method('isValidUrl')
            ->willReturn(true);

        $config = $this->createStub(\Youthweb\BBCodeParser\Config::class);

        $config->method('getValidation')
            ->willReturn($validation);

        $config->method('get')
            ->willReturnMap(
                [
                    ['callbacks.image.force_check', null, true],
                ],
            );

        $definition = new Image($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public static function dataProvider(): \Iterator
    {
        yield [
            'http://example.org/image.jpg',
            null,
            '<a target="_blank" href="http://example.org/image.jpg"><img class="img-responsive" border="0" src="http://example.org/image.jpg" alt="image" /></a>',
        ];
        yield [
            'example.org/image.jpg',
            null,
            '<a target="_blank" href="http://example.org/image.jpg"><img class="img-responsive" border="0" src="http://example.org/image.jpg" alt="image" /></a>',
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
