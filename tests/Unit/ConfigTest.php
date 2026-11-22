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

use Youthweb\BBCodeParser\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Config::class)]
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('SetterDataProvider')]
    public function testSetter(string $key, null $default, string $value): void
    {
        $validation = $this->createStub(\Youthweb\BBCodeParser\ValidationInterface::class);

        $config = new Config($validation);

        $this->assertSame($default, $config->get($key));

        $this->assertSame($config, $config->set($key, $value));

        $this->assertSame($value, $config->get($key));
    }

    public static function SetterDataProvider()
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

    #[Test]
    public function testGetter(): void
    {
        $validation = $this->createStub(\Youthweb\BBCodeParser\ValidationInterface::class);

        $config = new Config($validation);
        $config->set('foo', 'bar');

        $this->assertSame('default', $config->get('foo.bar', 'default'));
    }
}
