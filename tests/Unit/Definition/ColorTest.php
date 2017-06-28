<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Color;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ColorTest extends \PHPUnit_Framework_TestCase
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
						array('callbacks.color_param.allowed_colors', null, ['Red', 'Yellow']),
						array('callbacks.color_param.default_color', null, 'Red'),
					)
				)
			);

		$definition = new Color($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * data provider
	 */
	public function dataProvider()
	{
		return [
			[
				'some text',
				null,
				'<span style="color:Red;">some text</span>',
			],
			[
				'',
				null,
				'',
			],
		];
	}
}
