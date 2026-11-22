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
use Youthweb\BBCodeParser\Definition\V;
use Youthweb\BBCodeParser\Filter\FilterException;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(V::class)]
class VTest extends \PHPUnit\Framework\TestCase
{
    use MockerTrait;

    #[DataProvider('dataProvider')]
    public function testAsHtml(string $text, null $attribute, string $expected): void
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.target', null, '_blank'],
                ['filter.video', null, false],
            ],
        );

        $definition = new V($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public static function dataProvider()
    {
        return [
            [
                'http://example.org/video.mp4',
                null,
                '<a target="_blank" href="http://example.org/video.mp4">http://example.org/video.mp4</a>',
            ],
            [
                'example.org/video.mp4',
                null,
                '<a target="_blank" href="http://example.org/video.mp4">example.org/video.mp4</a>',
            ],
            [
                'invalid url',
                null,
                'invalid url',
            ],
            [
                '',
                null,
                '',
            ],
        ];
    }

    #[Test]
    public function testAsHtmlWithFilter(): void
    {
        $text = 'http://example.org/video.mp4';
        $attribute = null;
        $expected = 'some value';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $filter = $this->createStub(\Youthweb\BBCodeParser\Filter\FilterInterface::class);

        $filter->method('execute')
            ->willReturn($expected);

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.target', null, '_blank'],
                ['filter.video', null, $filter],
            ],
        );

        $definition = new V($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[Test]
    public function testAsHtmlWithFilterException(): void
    {
        $text = 'http://example.org/video.mp4';
        $attribute = null;
        $expected = '<a target="_blank" href="http://example.org/video.mp4">http://example.org/video.mp4</a>';
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $filter = $this->createStub(\Youthweb\BBCodeParser\Filter\FilterInterface::class);

        $filter->method('execute')
            ->willThrowException(new FilterException());

        $config = $this->buildConfigStub(
            [
                ['callbacks.url_content.target', null, '_blank'],
                ['filter.video', null, $filter],
            ],
        );

        $definition = new V($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }
}
