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
