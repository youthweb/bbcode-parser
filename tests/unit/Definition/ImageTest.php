<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Image;
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
