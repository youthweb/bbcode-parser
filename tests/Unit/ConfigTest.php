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
