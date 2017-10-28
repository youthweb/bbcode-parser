<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\V;
use Youthweb\BBCodeParser\Filter\FilterException;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class VTest extends \PHPUnit\Framework\TestCase
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
						array('callbacks.url_content.target', null, '_blank'),
						array('filter.video', null, false),
					)
				)
			);

		$definition = new V($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * data provider
	 */
	public function dataProvider()
	{
		return [
			[
				'http://example.org/video.mp4',
				null,
				'<a target="_blank" href="http://example.org/video.mp4">http://example.org/video.mp4</a>',
			],
			[
				'example.org/video.mp4',
				null,
				'<a target="_blank" href="http://example.org/video.mp4">example.org/video.mp4</a>',
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

	/**
	 * @test
	 */
	public function testAsHtmlWithFilter()
	{
		$text = 'http://example.org/video.mp4';
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
						array('callbacks.url_content.target', null, '_blank'),
						array('filter.video', null, $filter),
					)
				)
			);

		$definition = new V($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithFilterException()
	{
		$text = 'http://example.org/video.mp4';
		$attribute = null;
		$expected = '<a target="_blank" href="http://example.org/video.mp4">http://example.org/video.mp4</a>';
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
						array('callbacks.url_content.target', null, '_blank'),
						array('filter.video', null, $filter),
					)
				)
			);

		$definition = new V($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}
}
