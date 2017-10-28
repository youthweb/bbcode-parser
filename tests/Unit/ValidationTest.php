<?php

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Tests\Fixtures\MockHttpStreamWrapper;
use Youthweb\BBCodeParser\Tests\Fixtures\ValidationMock;
use Youthweb\BBCodeParser\Validation;

class ValidationTest extends \PHPUnit\Framework\TestCase
{
	public function setUp()
	{
		stream_wrapper_unregister('http');
		stream_wrapper_register(
			'http',
			'Youthweb\BBCodeParser\Tests\Fixtures\MockHttpStreamWrapper'
		) or die('Failed to register protocol');
	}

	public function tearDown()
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
					array(
						array('cacheitempool', null, null),
					)
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
