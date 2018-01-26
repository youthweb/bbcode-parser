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
use Youthweb\BBCodeParser\Definition\Code;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class CodeTest extends \PHPUnit\Framework\TestCase
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

        $definition = new Code();

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public function dataProvider()
    {
        return [
            [
                '<span style="color:Red;">some text mit Umlauten äöü</span>',
                null,
                '<!-- no_p --><pre><code>&lt;span style=&quot;color:Red;&quot;&gt;some text mit Umlauten äöü&lt;/span&gt;</code></pre><!-- no_p -->',
            ],
            // Do not double encode
            [
                '&lt;span style=&quot;color:Red;&quot;&gt;some text mit Umlauten äöü&lt;/span&gt;',
                null,
                '<!-- no_p --><pre><code>&lt;span style=&quot;color:Red;&quot;&gt;some text mit Umlauten äöü&lt;/span&gt;</code></pre><!-- no_p -->',
            ],
            [
                '',
                null,
                '<!-- no_p --><pre><code></code></pre><!-- no_p -->',
            ],
        ];
    }
}
