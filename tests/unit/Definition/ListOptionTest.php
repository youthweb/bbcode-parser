<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\ListOption;

class ListOptionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function testAsHtmlWithoutParam()
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

		$listDefinition = new ListOption();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithParamA()
	{
		$text     = '[*]Hello World!
[*]foobar';
		$expected = '<!-- no_p --><ol type="a">'.PHP_EOL.
"\t".'<li>Hello World!</li>'.PHP_EOL.
"\t".'<li>foobar</li>'.PHP_EOL.
'</ol>'.PHP_EOL.
'<!-- no_p -->';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn('a');

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListOption();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithParamAAsArray()
	{
		$text     = '[*]Hello World!
[*]foobar';
		$expected = '<!-- no_p --><ol type="a">'.PHP_EOL.
"\t".'<li>Hello World!</li>'.PHP_EOL.
"\t".'<li>foobar</li>'.PHP_EOL.
'</ol>'.PHP_EOL.
'<!-- no_p -->';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn(['a']);

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListOption();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithParamCapitalA()
	{
		$text     = '[*]Hello World!
[*]foobar';
		$expected = '<!-- no_p --><ol type="A">'.PHP_EOL.
"\t".'<li>Hello World!</li>'.PHP_EOL.
"\t".'<li>foobar</li>'.PHP_EOL.
'</ol>'.PHP_EOL.
'<!-- no_p -->';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn('A');

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListOption();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithParamI()
	{
		$text     = '[*]Hello World!
[*]foobar';
		$expected = '<!-- no_p --><ol type="i">'.PHP_EOL.
"\t".'<li>Hello World!</li>'.PHP_EOL.
"\t".'<li>foobar</li>'.PHP_EOL.
'</ol>'.PHP_EOL.
'<!-- no_p -->';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn('i');

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListOption();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithParamCapitalI()
	{
		$text     = '[*]Hello World!
[*]foobar';
		$expected = '<!-- no_p --><ol type="I">'.PHP_EOL.
"\t".'<li>Hello World!</li>'.PHP_EOL.
"\t".'<li>foobar</li>'.PHP_EOL.
'</ol>'.PHP_EOL.
'<!-- no_p -->';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn('I');

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListOption();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}

	/**
	 * @test
	 */
	public function testAsHtmlWithParamNumber()
	{
		$text     = '[*]Hello World!
[*]foobar';
		$expected = '<!-- no_p --><ol start="5">'.PHP_EOL.
"\t".'<li>Hello World!</li>'.PHP_EOL.
"\t".'<li>foobar</li>'.PHP_EOL.
'</ol>'.PHP_EOL.
'<!-- no_p -->';

		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$text])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn('5');

		$elementNode->method('getChildren')
			->willReturn([$child]);

		$listDefinition = new ListOption();

		$this->assertSame($expected, $listDefinition->asHtml($elementNode));
	}
}
