<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\ListDefinition;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ListDefinitionTest extends \PHPUnit_Framework_TestCase
{
	use MockerTrait;

	/**
	 * @dataProvider dataProvider
	 */
	public function testAsHtml($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$definition = new ListDefinition();

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * data provider
	 */
	public function dataProvider()
	{
		return [
			[
				'[*]Hello World!
[*]foobar',
				null,
				'<!-- no_p --><ul type="disc">'.PHP_EOL.
				"\t".'<li>Hello World!</li>'.PHP_EOL.
				"\t".'<li>foobar</li>'.PHP_EOL.
				'</ul>'.PHP_EOL.
				'<!-- no_p -->',
			],
			[
				'',
				null,
				'',
			],
			[
				'line1
line2',
				null,
				'<!-- no_p --><ul type="disc">'.PHP_EOL.
				"\t".'<li>line1
line2</li>'.PHP_EOL.
				'</ul>'.PHP_EOL.
				'<!-- no_p -->',
			],
		];
	}
}
