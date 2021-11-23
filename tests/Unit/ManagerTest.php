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

namespace Youthweb\BBCodeParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Visitor\VisitorCollectionInterface;
use Youthweb\BBCodeParser\Visitor\VisitorInterface;

class ManagerTest extends TestCase
{
    /**
     * @test
     */
    public function testParseWithoutDefinitions()
    {
        $manager = new Manager();

        $config = [
            'parse_headlines' => false,
            'parse_default' => false,
        ];

        $text = '[b]test[/b]';

        $this->assertSame($text, $manager->parse($text, $config));
    }

    /**
     * @test
     */
    public function parseWillUseTheVisitorCollection()
    {
        $visitor = $this->createMock(VisitorInterface::class);

        $visitorCollection = $this->createMock(VisitorCollectionInterface::class);
        $visitorCollection->expects($this->once())->method('getVisitors')->willReturn([$visitor]);

        $manager = new Manager($visitorCollection);

        $config = [
            'parse_headlines' => false,
            'parse_default' => false,
        ];

        $text = '[b]test[/b]';

        $this->assertSame($text, $manager->parse($text, $config));
    }
}
