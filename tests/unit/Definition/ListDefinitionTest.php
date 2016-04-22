<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\ListDefinition;

class ListDefinitionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function testAsHtml()
	{
		$text     = '[*]Hello World!
[*]foobar';
		$expected = '<!-- no_p --><ul type="disc">'.PHP_EOL.
"\t".'<li>Hello World!</li>'.PHP_EOL.
"\t".'<li>foobar</li>'.PHP_EOL.
'</ul>'.PHP_EOL.
'<!-- no_p -->';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn(null);

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListDefinition();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithoutContent()
	{
		$text     = '';
		$expected = '';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn(null);

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListDefinition();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithInvalidContent()
	{
		$text     = 'line1
line2';
$expected = '<!-- no_p --><ul type="disc">'.PHP_EOL.
"\t".'<li>line1
line2</li>'.PHP_EOL.
'</ul>'.PHP_EOL.
'<!-- no_p -->';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn(null);

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListDefinition();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}
}
