<?php
/*
 * A BBCode-to-HTML parser for youthweb.net
 * Copyright (C) 2016-2019  Youthweb e.V. <info@youthweb.net>

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
