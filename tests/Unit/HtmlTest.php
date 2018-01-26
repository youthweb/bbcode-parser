<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Html;

class HtmlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider ImageDataProvider
     *
     * @param mixed $src
     * @param mixed $attr
     * @param mixed $expected
     */
    public function testImg($src, $attr, $expected)
    {
        $this->assertSame($expected, Html::img($src, $attr));
    }

    /**
     * @ImageDataProvider
     */
    public function ImageDataProvider()
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
}
