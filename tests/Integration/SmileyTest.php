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

namespace Youthweb\BBCodeParser\Tests\Integration;

use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Visitor\VisitorCollection;
use Youthweb\BBCodeParser\Visitor\VisitorSmiley;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Manager::class)]
final class SmileyTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    public function testParseSmileyCode(): void
    {
        $text     = 'My mistake :-[';
        $expected = '<p>My mistake <img src="https://youthweb.net/vendor/smilies/49_2.gif" alt=":-[" title=":-[" /></p>';

        $parser = new Manager();

        $config = [
            'visitor' => [
                'smiley' => new VisitorSmiley(),
            ],
        ];

        $this->assertSame($expected, $parser->parse($text, $config));
    }

    #[Test]
    #[DataProvider('provideSmileyExamples')]
    public function parseSmileyWithCustomVisitor(string $text, string $expected): void
    {
        $visitor = new VisitorSmiley();

        $collection = new VisitorCollection();
        $collection->addVisitor($visitor);

        $parser = new Manager($collection);

        $config = [
            'parse_smilies' => false,
            'parse_urls' => false,
        ];

        $this->assertSame($expected, $parser->parse($text, $config));
    }

    public static function provideSmileyExamples(): \Iterator
    {
        yield [
            'My mistake :-[',
            '<p>My mistake <img src="https://youthweb.net/vendor/smilies/49_2.gif" alt=":-[" title=":-[" /></p>',
        ];
        yield [
            'My mistake <span> :-[',
            '<p>My mistake <span> <img src="https://youthweb.net/vendor/smilies/49_2.gif" alt=":-[" title=":-[" /></p>',
        ];
        yield [
            '[b]My mistake :-[[/b]',
            '<p><b>My mistake <img src="https://youthweb.net/vendor/smilies/49_2.gif" alt=":-[" title=":-[" /></b></p>',
        ];
    }
}
