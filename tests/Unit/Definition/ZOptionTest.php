<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\ZOption;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class ZOptionTest extends \PHPUnit\Framework\TestCase
{
	use MockerTrait;

	/**
	 * @dataProvider dataProvider
	 */
	public function testAsHtml($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$definition = new ZOption();

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
