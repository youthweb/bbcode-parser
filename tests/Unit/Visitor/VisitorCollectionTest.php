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

namespace Youthweb\BBCodeParser\Tests\Unit\Visitor;

use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Visitor\VisitorCollection;
use Youthweb\BBCodeParser\Visitor\VisitorInterface;

class VisitorCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function getVisitorsReturnsEmptyArray()
    {
        $collection = new VisitorCollection();

        $this->assertSame([], $collection->getVisitors());
    }

    /**
     * @test
     */
    public function getVisitorsReturnsArrayWithOneElement()
    {
        $visitor = $this->createMock(VisitorInterface::class);

        $collection = new VisitorCollection();
        $collection->addVisitor($visitor);

        $this->assertContainsOnlyInstancesOf(VisitorInterface::class, $collection->getVisitors());
        $this->assertCount(1, $collection->getVisitors());
    }

    /**
     * @test
     */
    public function getVisitorsReturnsArrayWithMultipleElements()
    {
        $visitor = $this->createMock(VisitorInterface::class);

        $collection = new VisitorCollection();
        $collection->addVisitor($visitor);
        $collection->addVisitor($visitor);
        $collection->addVisitor($visitor);

        $this->assertContainsOnlyInstancesOf(VisitorInterface::class, $collection->getVisitors());
        $this->assertCount(3, $collection->getVisitors());
    }
}
