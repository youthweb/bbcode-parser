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

namespace Youthweb\BBCodeParser\Tests\Integration\Parser;

use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Parser\CommonMarkParser;

class CommonMarkParserTest extends TestCase
{
    /**
     * @dataProvider provideBbcodeAndHtml
     */
    public function testParseBbcodeToHtml(string $text, string $expected)
    {
        $parser = CommonMarkParser::create();

        $this->assertSame($expected, $parser->parseBbcodeToHtml($text));
    }

    public function provideBbcodeAndHtml()
    {
        return [
            [
                'Hello World!',
                '<p>Hello World!</p>'.\PHP_EOL,
            ],
            [
                'Hello World! </div>',
                '<p>Hello World! &lt;/div&gt;</p>'.\PHP_EOL,
            ],
            [
                'Hello World! <img src="javascript:alert(\'XSS\')">',
                '<p>Hello World! &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</p>'.\PHP_EOL,
            ],
            [
                '**Hello World!**',
                '<p><b>Hello World!</b></p>'.\PHP_EOL,
            ],
        ];
    }
}
