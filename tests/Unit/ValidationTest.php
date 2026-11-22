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

use Youthweb\BBCodeParser\Tests\Fixtures\MockHttpStreamWrapper;
use Youthweb\BBCodeParser\Tests\Fixtures\ValidationMock;
use Youthweb\BBCodeParser\Validation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Validation::class)]
class ValidationTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        stream_wrapper_unregister('http');
        if (!stream_wrapper_register(
            'http',
            \Youthweb\BBCodeParser\Tests\Fixtures\MockHttpStreamWrapper::class,
        )) {
            die('Failed to register protocol');
        }
    }

    public function tearDown(): void
    {
        stream_wrapper_restore('http');

        ValidationMock::resetImageCounter();
    }

    public function getConfigMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        $config = $this->getMockBuilder(\Youthweb\BBCodeParser\Config::class)
            ->onlyMethods(['get'])
            ->getMock();

        $config->expects($this->any())
            ->method('get')
            ->willReturnMap(
                [
                    ['cacheitempool', null, null],
                ],
            );

        return $config;
    }

    #[Test]
    public function testValidImageUrl(): void
    {
        MockHttpStreamWrapper::$mockMetaData = [
            'Content-Type: image/jpeg',
        ];
        MockHttpStreamWrapper::$mockResponseCode = 'HTTP/1.1 200 OK';

        ValidationMock::resetImageCounter();

        $validation = new Validation($this->getConfigMock());

        $this->assertTrue($validation->isValidImageUrl('http://example.org/image.jpg', true));
    }

    #[Test]
    public function testMultipleValidImageUrl(): void
    {
        MockHttpStreamWrapper::$mockMetaData = [
            'Content-Type: image/jpeg',
        ];
        MockHttpStreamWrapper::$mockResponseCode = 'HTTP/1.1 200 OK';

        ValidationMock::resetImageCounter();

        $validation = new Validation($this->getConfigMock());

        $this->assertTrue($validation->isValidImageUrl('http://example.org/image.jpg', true));
        $this->assertFalse($validation->isValidImageUrl('http://example.org/image2.jpg', true));
    }

    #[Test]
    public function testCachedMultipleValidImageUrl(): void
    {
        MockHttpStreamWrapper::$mockMetaData = [
            'Content-Type: image/jpeg',
        ];
        MockHttpStreamWrapper::$mockResponseCode = 'HTTP/1.1 200 OK';

        ValidationMock::resetImageCounter();

        $validation = new Validation($this->getConfigMock());

        $this->assertTrue($validation->isValidImageUrl('http://example.org/image.jpg', true));
        $this->assertTrue($validation->isValidImageUrl('http://example.org/image.jpg', true));
    }

    #[Test]
    public function testValidImageUrlForceless(): void
    {
        $validation = new Validation($this->getConfigMock());

        $this->assertTrue($validation->isValidImageUrl('http://example.org/image.jpg', false));
    }

    #[Test]
    public function testValidImageUrlForcelessWithInvalidUrl(): void
    {
        $validation = new Validation($this->getConfigMock());

        $this->assertFalse($validation->isValidImageUrl('foobar', false));
    }

    /**
     * test valid url
     */
    #[DataProvider('providerValidUrl')]
    public function testValidUrl(string $url, bool $expected): void
    {
        $validation = new Validation($this->getConfigMock());

        $this->assertSame($expected, $validation->isValidUrl($url));
    }

    public static function providerValidUrl()
    {
        return [
            [
                'http://example.com',
                true,
            ],
            [
                'http://www.example.com/irgend/eine/lange/url/die/gek&uuml;rzt/werden/soll.html',
                true,
            ],
            [
                'http://www.example.com/irgend/eine/lange/url/die/gek%C3%BCrzt/werden/soll.html',
                true,
            ],
            [
                'http://www.example.com/irgend/eine/lange/url/die/gekürzt/werden/soll.html',
                false,
            ],
        ];
    }
}
