<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
