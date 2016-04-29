<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Image;
use Youthweb\BBCodeParser\Filter\FilterException;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ImageTest extends \PHPUnit_Framework_TestCase
{
	use MockerTrait;

	/**
	 * @dataProvider dataProvider
	 */
	public function testAsHtml($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->expects($this->any())
			->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.image.force_check', null, false),
					)
				)
			);

		$definition = new Image($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithFilter()
	{
		$text = 'http://example.org/this/is/a/very/long/url.with?query=params';
		$attribute = null;
		$expected = 'some value';
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$filter = $this->getMockBuilder('Youthweb\BBCodeParser\Filter\FilterInterface')
			->setMethods(['setConfig', 'execute'])
			->getMock();

		$filter->expects($this->any())
			->method('execute')
			->willReturn($expected);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->expects($this->any())
			->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.image.force_check', null, false),
						array('filter.image', null, $filter),
					)
				)
			);

		$definition = new Image($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithFilterException()
	{
		$text = 'http://example.org/image.jpg';
		$attribute = null;
		$expected = '<a target="_blank" href="http://example.org/image.jpg"><img class="img-responsive" border="0" src="http://example.org/image.jpg" alt="image" /></a>';
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$filter = $this->getMockBuilder('Youthweb\BBCodeParser\Filter\FilterInterface')
			->setMethods(['setConfig', 'execute'])
			->getMock();

		$filter->expects($this->any())
			->method('execute')
			->willThrowException(new FilterException);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->expects($this->any())
			->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.image.force_check', null, false),
						array('filter.image', null, $filter),
					)
				)
			);

		$definition = new Image($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithUrlParent()
	{
		$text = 'http://example.org/image.jpg';
		$attribute = null;
		$expected = '<img class="img-responsive" border="0" src="http://example.org/image.jpg" alt="image" />';
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$elementNode->expects($this->any())
			->method('closestParentOfType')
			->willReturn(true);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->expects($this->any())
			->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.image.force_check', null, false),
					)
				)
			);

		$definition = new Image($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithForceCheck()
	{
		$text = 'http://example.org/not-an-image.txt';
		$attribute = null;
		$expected = 'http://example.org/not-an-image.txt';
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$elementNode->expects($this->any())
			->method('closestParentOfType')
			->willReturn(true);

		$validation = $this->getMockBuilder('Youthweb\BBCodeParser\ValidationInterface')
			->getMock();

		$validation->expects($this->any())
			->method('isValidImageUrl')
			->willReturn(false);

		$validation->expects($this->any())
			->method('isValidUrl')
			->willReturn(true);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get', 'getValidation'])
			->getMock();

		$config->expects($this->any())
			->method('getValidation')
			->willReturn($validation);

		$config->expects($this->any())
			->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.image.force_check', null, true),
					)
				)
			);

		$definition = new Image($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithForceCheckAndAnchor()
	{
		$text = 'http://example.org/not-an-image.txt';
		$attribute = null;
		$expected = '<a href="http://example.org/not-an-image.txt">http://example.org/not-an-image.txt</a>';
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$elementNode->expects($this->any())
			->method('closestParentOfType')
			->willReturn(false);

		$validation = $this->getMockBuilder('Youthweb\BBCodeParser\ValidationInterface')
			->getMock();

		$validation->expects($this->any())
			->method('isValidImageUrl')
			->willReturn(false);

		$validation->expects($this->any())
			->method('isValidUrl')
			->willReturn(true);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get', 'getValidation'])
			->getMock();

		$config->expects($this->any())
			->method('getValidation')
			->willReturn($validation);

		$config->expects($this->any())
			->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.image.force_check', null, true),
					)
				)
			);

		$definition = new Image($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * data provider
	 */
	public function dataProvider()
	{
		return [
			[
				'http://example.org/image.jpg',
				null,
				'<a target="_blank" href="http://example.org/image.jpg"><img class="img-responsive" border="0" src="http://example.org/image.jpg" alt="image" /></a>',
			],
			[
				'invalid url',
				null,
				'invalid url',
			],
			[
				'',
				null,
				'',
			],
		];
	}
}
