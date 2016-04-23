<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\ListOption;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ListOptionTest extends \PHPUnit_Framework_TestCase
{
	use MockerTrait;

	/**
	 * @dataProvider dataProvider
	 */
	public function testAsHtml($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$definition = new ListOption();

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
				'[*]Hello World!
[*]foobar',
				'a',
				'<!-- no_p --><ol type="a">'.PHP_EOL.
				"\t".'<li>Hello World!</li>'.PHP_EOL.
				"\t".'<li>foobar</li>'.PHP_EOL.
				'</ol>'.PHP_EOL.
				'<!-- no_p -->',
			],
			[
				'[*]Hello World!
[*]foobar',
				['a'],
				'<!-- no_p --><ol type="a">'.PHP_EOL.
				"\t".'<li>Hello World!</li>'.PHP_EOL.
				"\t".'<li>foobar</li>'.PHP_EOL.
				'</ol>'.PHP_EOL.
				'<!-- no_p -->',
			],
			[
				'[*]Hello World!
[*]foobar',
				'A',
				'<!-- no_p --><ol type="A">'.PHP_EOL.
				"\t".'<li>Hello World!</li>'.PHP_EOL.
				"\t".'<li>foobar</li>'.PHP_EOL.
				'</ol>'.PHP_EOL.
				'<!-- no_p -->',
			],
			[
				'[*]Hello World!
[*]foobar',
				'i',
				'<!-- no_p --><ol type="i">'.PHP_EOL.
				"\t".'<li>Hello World!</li>'.PHP_EOL.
				"\t".'<li>foobar</li>'.PHP_EOL.
				'</ol>'.PHP_EOL.
				'<!-- no_p -->',
			],
			[
				'[*]Hello World!
[*]foobar',
				'I',
				'<!-- no_p --><ol type="I">'.PHP_EOL.
				"\t".'<li>Hello World!</li>'.PHP_EOL.
				"\t".'<li>foobar</li>'.PHP_EOL.
				'</ol>'.PHP_EOL.
				'<!-- no_p -->',
			],
			[
				'[*]Hello World!
[*]foobar',
				'5',
				'<!-- no_p --><ol start="5">'.PHP_EOL.
				"\t".'<li>Hello World!</li>'.PHP_EOL.
				"\t".'<li>foobar</li>'.PHP_EOL.
				'</ol>'.PHP_EOL.
				'<!-- no_p -->',
			],
		];
	}
}
