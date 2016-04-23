<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Q;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class QTest extends \PHPUnit_Framework_TestCase
{
	use MockerTrait;

	/**
	 * @dataProvider dataProvider
	 */
	public function testAsHtml($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$listDefinition = new Q();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * data provider
	 */
	public function dataProvider()
	{
		return [
			[
				'quote content',
				null,
				'<blockquote title="Zitat"><cite></cite>quote content</blockquote>',
			],
			[
				'quote content',
				'someone',
				'<blockquote title="Zitat"><cite>someone</cite>quote content</blockquote>',
			],
			[
				'quote content',
				['someone'],
				'<blockquote title="Zitat"><cite>someone</cite>quote content</blockquote>',
			],
			[
				'<blockquote title="Zitat"><cite>first</cite>content 1</blockquote>content 2',
				'second',
				'<blockquote title="Zitat"><blockquote title="Zitat"><cite>first</cite>content 1</blockquote><cite>second</cite>content 2</blockquote>',
			],
		];
	}
}
