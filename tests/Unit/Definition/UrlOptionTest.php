<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\UrlOption;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class UrlOptionTest extends \PHPUnit\Framework\TestCase
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
						array('callbacks.url_content.short_url', null, false),
						array('callbacks.url_content.short_url_length', null, 55),
						array('callbacks.url_content.target', null, '_blank'),
					)
				)
			);

		$definition = new UrlOption($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithShortenLongUrl()
	{
		$text = 'a very long text, that should not be shorten';
		$attribute = 'http://example.org/this/is/a/very/long/url.with?query=params';
		$expected = '<a target="_blank" href="http://example.org/this/is/a/very/long/url.with?query=params">a very long text, that should not be shorten</a>';

		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.url_content.short_url', null, true),
						array('callbacks.url_content.short_url_length', null, 30),
						array('callbacks.url_content.target', null, '_blank'),
					)
				)
			);

		$definition = new UrlOption($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithShortenShortUrl()
	{
		$text = 'a very long text, that should not be shorten';
		$attribute = 'http://example.org/this/is/a/very/long/url.with?query=params';
		$expected = '<a target="_blank" href="http://example.org/this/is/a/very/long/url.with?query=params">a very long text, that should not be shorten</a>';

		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.url_content.short_url', null, true),
						array('callbacks.url_content.short_url_length', null, 20),
						array('callbacks.url_content.target', null, '_blank'),
					)
				)
			);

		$definition = new UrlOption($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithoutTarget()
	{
		$text = 'a very long text, that should not be shorten';
		$attribute = 'http://example.org/this/is/a/very/long/url.with?query=params';
		$expected = '<a href="http://example.org/this/is/a/very/long/url.with?query=params">a very long text, that should not be shorten</a>';

		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.url_content.short_url', null, true),
						array('callbacks.url_content.short_url_length', null, 55),
						array('callbacks.url_content.target', null, null),
					)
				)
			);

		$definition = new UrlOption($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithTargetOnYouthwebUrl()
	{
		$text = 'a very long text, that should not be shorten';
		$attribute = 'http://youthweb.net/this/is/a/very/long/url.with?query=params';
		$expected = '<a href="http://youthweb.net/this/is/a/very/long/url.with?query=params">a very long text, that should not be shorten</a>';

		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.url_content.short_url', null, true),
						array('callbacks.url_content.short_url_length', null, 55),
						array('callbacks.url_content.target', null, '_blank'),
					)
				)
			);

		$definition = new UrlOption($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithTargetOnYouthwebUrlWithoutHttp()
	{
		$text = 'a very long text, that should not be shorten';
		$attribute = 'ftp://youthweb.net/this/is/a/very/long/url.with?query=params';
		$expected = '<a target="_blank" href="ftp://youthweb.net/this/is/a/very/long/url.with?query=params">a very long text, that should not be shorten</a>';

		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->method('get')
			->will(
				$this->returnValueMap(
					array(
						array('callbacks.url_content.short_url', null, true),
						array('callbacks.url_content.short_url_length', null, 55),
						array('callbacks.url_content.target', null, '_blank'),
					)
				)
			);

		$definition = new UrlOption($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * data provider
	 */
	public function dataProvider()
	{
		return [
			[
				'example.org',
				'http://example.org',
				'<a target="_blank" href="http://example.org">example.org</a>',
			],
			[
				'example.org',
				['http://example.org'],
				'<a target="_blank" href="http://example.org">example.org</a>',
			],
			[
				'text',
				'invalid url',
				'text',
			],
			[
				'',
				'',
				'',
			],
		];
	}
}
