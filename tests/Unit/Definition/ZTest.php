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
use Youthweb\BBCodeParser\Definition\Z;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ZTest extends \PHPUnit\Framework\TestCase
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

        $definition = new Z();

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public function dataProvider()
    {
        return [
            [
                'quote content',
                null,
                '<blockquote title="Zitat"><cite></cite>quote content</blockquote>',
            ],
            [
                'quote content',
                null,
                '<blockquote title="Zitat"><cite></cite>quote content</blockquote>',
            ],
            [
                '<blockquote title="Zitat"><cite>first</cite>content 1</blockquote>content 2',
                null,
                '<blockquote title="Zitat"><blockquote title="Zitat"><cite>first</cite>content 1</blockquote><cite></cite>content 2</blockquote>',
            ],
        ];
    }
}
