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
use Youthweb\BBCodeParser\Definition\Noparse;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class NoparseTest extends \PHPUnit\Framework\TestCase
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

        $definition = new Noparse();

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public function dataProvider()
    {
        return [
            [
                '<span style="color:Red;">some text</span>',
                null,
                '&lt;span style=&quot;color:Red;&quot;&gt;some text&lt;/span&gt;',
            ],
            [
                '',
                null,
                '',
            ],
            // Do not double encode
            [
                '&lt;h7&gt;Header 7&lt;/h7&gt;',
                [],
                '&lt;h7&gt;Header 7&lt;/h7&gt;',
            ],
        ];
    }
}
