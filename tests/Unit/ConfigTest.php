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

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider SetterDataProvider
     *
     * @param mixed $key
     * @param mixed $default
     * @param mixed $value
     */
    public function testSetter($key, $default, $value)
    {
        $validation = $this->getMockBuilder('Youthweb\BBCodeParser\ValidationInterface')
            ->getMock();

        $config = new Config($validation);

        $this->assertSame($default, $config->get($key));

        $this->assertSame($config, $config->set($key, $value));

        $this->assertSame($value, $config->get($key));
    }

    /**
     * @SetterDataProvider
     */
    public function SetterDataProvider()
    {
        return [
            [
                'foobar',
                null,
                'baz',
            ],
            [
                'foo.bar',
                null,
                'baz',
            ],
            [
                'foo.0.bar',
                null,
                'baz',
            ],
        ];
    }

    /**
     * @test
     */
    public function testGetter()
    {
        $validation = $this->getMockBuilder('Youthweb\BBCodeParser\ValidationInterface')
            ->getMock();

        $config = new Config($validation);
        $config->set('foo', 'bar');

        $this->assertSame('default', $config->get('foo.bar', 'default'));
    }
}
