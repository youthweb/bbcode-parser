<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Quote;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class QuoteTest extends \PHPUnit\Framework\TestCase
{
	use MockerTrait;

	/**
	 * @dataProvider dataProvider
	 */
	public function testAsHtml($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$definition = new Quote();

		$this->assertSame($expected, $definition->asHtml($elementNode));
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
				null,
				'<blockquote title="Zitat"><cite></cite>quote content</blockquote>',
			],
			[
				'<blockquote title="Zitat"><cite>first</cite>content 1</blockquote>content 2',
				null,
				'<blockquote title="Zitat"><blockquote title="Zitat"><cite>first</cite>content 1</blockquote><cite></cite>content 2</blockquote>',
			],
		];
	}
}
