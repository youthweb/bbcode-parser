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
use Youthweb\BBCodeParser\Definition\ListDefinition;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ListDefinitionTest extends \PHPUnit\Framework\TestCase
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

        $definition = new ListDefinition();

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
                '',
                null,
                '',
            ],
            [
                'line1
line2',
                null,
                '<!-- no_p --><ul type="disc">' . PHP_EOL .
                "\t" . '<li>line1
line2</li>' . PHP_EOL .
                '</ul>' . PHP_EOL .
                '<!-- no_p -->',
            ],
        ];
    }
}
