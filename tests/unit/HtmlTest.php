<?php

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Html;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider ImageDataProvider
	 */
	public function testImg($src, $attr, $expected)
	{
		$this->assertSame($expected, Html::img($src, $attr));
	}

	/**
	 * @ImageDataProvider
	 */
	public function ImageDataProvider()
	{
		return [
			[
				'http://example.org/image.jpg',
				['hidden' => false],
				'<img src="http://example.org/image.jpg" alt="image" />',
			],
			[
				'example.org/image.jpg',
				['hidden'],
				'<img hidden="hidden" src="/example.org/image.jpg" alt="image" />',
			],
		];
	}
}
