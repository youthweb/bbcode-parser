<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\Tests\Integration;

use Youthweb\BBCodeParser\Manager;

class QuoteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function testParseQutoeCode()
    {
        $text     = '[q="Albert Einstein"]*bäh*[/q]';
        $expected = '<p><blockquote title="Zitat"><cite>Albert Einstein</cite>*bäh*</blockquote></p>';

        $parser = new Manager();

        $this->assertSame($parser->parse($text), $expected);
    }
}
