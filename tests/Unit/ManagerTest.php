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

use Youthweb\BBCodeParser\Manager;

class ManagerTest extends \PHPUnit\Framework\TestCase
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
}
