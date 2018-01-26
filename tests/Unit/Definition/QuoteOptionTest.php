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
use Youthweb\BBCodeParser\Definition\QuoteOption;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class QuoteOptionTest extends \PHPUnit\Framework\TestCase
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

        $definition = new QuoteOption();

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
                'someone',
                '<blockquote title="Zitat"><cite>someone</cite>quote content</blockquote>',
            ],
            [
                'quote content',
                ['someone'],
                '<blockquote title="Zitat"><cite>someone</cite>quote content</blockquote>',
            ],
            [
                '<blockquote title="Zitat"><cite>first</cite>content 1</blockquote>content 2',
                'second',
                '<blockquote title="Zitat"><blockquote title="Zitat"><cite>first</cite>content 1</blockquote><cite>second</cite>content 2</blockquote>',
            ],
        ];
    }
}
