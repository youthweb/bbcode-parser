<?php

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Tests\Fixtures\MockHttpStreamWrapper;
use Youthweb\BBCodeParser\Validation;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		stream_wrapper_unregister('http');
		stream_wrapper_register(
			'http',
			'Youthweb\BBCodeParser\Tests\Fixtures\MockHttpStreamWrapper'
		) or die('Failed to register protocol');
	}

	/**
	* Makes a mock stream that returns expected values.
	* @param string $data response body
	* @param string $code HTTP response code
	*/
	public function getMockStream($data, $code='HTTP/1.1 200 OK')
	{
		MockHttpStreamWrapper::$mockBodyData = $data;
		MockHttpStreamWrapper::$mockResponseCode = $code;
		$context = stream_context_create(
			array(
				'http' => array(
					'method' => 'GET'
				)
			)
		);
		$stream = fopen('http://example.com', 'r', false, $context);
		return $stream;
	}

	public function tearDown()
	{
		stream_wrapper_restore('http');
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

		$validation = new Validation();

		$this->assertSame(true, $validation->isValidImageUrl('http://example.org/image.jpg', true));
	}

	/**
	 * @test
	 */
	public function testValidImageUrlForceless()
	{
		$validation = new Validation();

		$this->assertSame(true, $validation->isValidImageUrl('http://example.org/image.jpg', false));
	}

	/**
	 * @test
	 */
	public function testValidImageUrlForcelessWithInvalidUrl()
	{
		$validation = new Validation();

		$this->assertSame(false, $validation->isValidImageUrl('foobar', false));
	}
}
