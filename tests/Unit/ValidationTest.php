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

class ValidationTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        stream_wrapper_unregister('http');
        stream_wrapper_register(
            'http',
            'Youthweb\BBCodeParser\Tests\Fixtures\MockHttpStreamWrapper'
        ) or die('Failed to register protocol');
    }

    public function tearDown(): void
    {
        stream_wrapper_restore('http');

        ValidationMock::resetImageCounter();
    }

    public function getConfigMock()
    {
        $config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
            ->setMethods(['get'])
            ->getMock();

        $config->expects($this->any())
            ->method('get')
            ->will(
                $this->returnValueMap(
                    [
                        ['cacheitempool', null, null],
                    ]
                )
            );

        return $config;
    }

    /**
     * @test
     */
    public function testValidImageUrl()
    {
        MockHttpStreamWrapper::$mockMetaData = [
            'Content-Type: image/jpeg',
        ];
        MockHttpStreamWrapper::$mockResponseCode = 'HTTP/1.1 200 OK';

        ValidationMock::resetImageCounter();

        $validation = new Validation($this->getConfigMock());

        $this->assertTrue($validation->isValidImageUrl('http://example.org/image.jpg', true));
    }

    /**
     * @test
     */
    public function testMultipleValidImageUrl()
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

    /**
     * @test
     */
    public function testCachedMultipleValidImageUrl()
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

    /**
     * @test
     */
    public function testValidImageUrlForceless()
    {
        $validation = new Validation($this->getConfigMock());

        $this->assertTrue($validation->isValidImageUrl('http://example.org/image.jpg', false));
    }

    /**
     * @test
     */
    public function testValidImageUrlForcelessWithInvalidUrl()
    {
        $validation = new Validation($this->getConfigMock());

        $this->assertFalse($validation->isValidImageUrl('foobar', false));
    }

    /**
     * test valid url
     *
     * @dataProvider providerValidUrl
     *
     * @param mixed $url
     * @param mixed $expected
     */
    public function testValidUrl($url, $expected)
    {
        $validation = new Validation($this->getConfigMock());

        $this->assertSame($expected, $validation->isValidUrl($url));
    }

    public function providerValidUrl()
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
