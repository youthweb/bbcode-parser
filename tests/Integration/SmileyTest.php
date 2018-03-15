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
use Youthweb\BBCodeParser\Visitor\VisitorCollection;
use Youthweb\BBCodeParser\Visitor\VisitorSmiley;

class SmileyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function testParseSmileyCode()
    {
        $text     = 'My mistake :-[';
        $expected = '<p>My mistake <img src="https://youthweb.net/vendor/smilies/49_2.gif" alt=":-[" title=":-[" /></p>';

        $parser = new Manager();

        $config = [
            'visitor' => [
                'smiley' => new VisitorSmiley()
            ]
        ];

        $this->assertSame($expected, $parser->parse($text, $config));
    }

    /**
     * @test
     */
    public function parseSmileyWithCustomVisitor()
    {
        $text     = 'My mistake :-[';
        $expected = '<p>My mistake <img src="https://youthweb.net/vendor/smilies/49_2.gif" alt=":-[" title=":-[" /></p>';

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
}
