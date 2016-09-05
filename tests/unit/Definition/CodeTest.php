<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Code;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class CodeTest extends \PHPUnit_Framework_TestCase
{
	use MockerTrait;

	/**
	 * @dataProvider dataProvider
	 */
	public function testAsHtml($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$definition = new Code();

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * data provider
	 */
	public function dataProvider()
	{
		return [
			[
				'<span style="color:Red;">some text</span>',
				null,
				'<code>&lt;span style=&quot;color:Red;&quot;&gt;some text&lt;/span&gt;</code>',
			],
			[
				'',
				null,
				'<code></code>',
			],
		];
	}
}
