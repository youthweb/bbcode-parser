<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Visitor;

use Youthweb\BBCodeParser\Visitor\VisitorUrl;

class VisitorUrlTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test create
	 */
	public function testCreate()
	{
		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->getMock();

		$visitor = new VisitorUrl();

		$config = new \Youthweb\BBCodeParser\Config;
		$visitor->setConfig($config);

		$this->assertInstanceOf('Youthweb\BBCodeParser\Visitor\VisitorInterface', $visitor);
	}

	/**
	 * @test visitDocumentElement
	 */
	public function testVisitDocumentElement()
	{
		$visitor = new VisitorUrl();

		$config = new \Youthweb\BBCodeParser\Config;
		$visitor->setConfig($config);

		$child = $this->getMockBuilder('JBBCode\Node')
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->getMock();

		$child->expects($this->once())
			->method('accept')
			->with($visitor)
			->willReturn(null);

		$element = $this->getMockBuilder('JBBCode\DocumentElement')
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->getMock();

		$element->method('getChildren')
			->willReturn([$child]);

		$visitor->visitDocumentElement($element);
	}

	/**
	 * @test visitElementNode
	 */
	public function testVisitElementNode()
	{
		$visitor = new VisitorUrl();

		$config = new \Youthweb\BBCodeParser\Config;
		$visitor->setConfig($config);

		$code_definition = $this->getMockBuilder('JBBCode\CodeDefinition')
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->getMock();

		$code_definition->method('parseContent')
			->willReturn(true);

		$child = $this->getMockBuilder('JBBCode\Node')
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->getMock();

		$child->expects($this->once())
			->method('accept')
			->with($visitor)
			->willReturn(null);

		$element = $this->getMockBuilder('JBBCode\ElementNode')
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->getMock();

		$element->method('getCodeDefinition')
			->willReturn($code_definition);

		$element->method('getChildren')
			->willReturn([$child]);

		$visitor->visitElementNode($element);
	}

	/**
	 * @dataProvider SmileyDataProvider
	 */
	public function testVisitTextNode($src, $expected)
	{
		$textnode = $this->getMockBuilder('JBBCode\TextNode')
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->getMock();

		$textnode->method('getValue')
			->willReturn($src);

		$textnode->expects($this->once())
			->method('setValue')
			->with($expected)
			->willReturn(null);

		$visitor = new VisitorUrl();

		$config = new \Youthweb\BBCodeParser\Config;
		$visitor->setConfig($config);

		$visitor->visitTextNode($textnode);
	}

	/**
	 * dataProvider
	 */
	public function SmileyDataProvider()
	{
		return [
			[
				'example.org',
				'<a target="_blank" href="http://example.org">http://example.org</a>',
			],
			[
				'Hier ist meine Webseite: example.org',
				'Hier ist meine Webseite: <a target="_blank" href="http://example.org">http://example.org</a>',
			],
		];
	}
}
