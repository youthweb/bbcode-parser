<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Visitor;

use Youthweb\BBCodeParser\Visitor\VisitorSmiley;

class VisitorSmileyTest extends \PHPUnit_Framework_TestCase
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

		$visitor = new VisitorSmiley();

		$visitor->setConfig($config);

		$this->assertInstanceOf('Youthweb\BBCodeParser\Visitor\VisitorInterface', $visitor);
	}

	/**
	 * @test visitDocumentElement
	 */
	public function visitDocumentElement()
	{
		$visitor = new VisitorSmiley();

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
	 * @dataProvider SmileyDataProvider
	 */
	public function testvisitTextNode($src, $expected)
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

		$visitor = new VisitorSmiley();

		$visitor->visitTextNode($textnode);
	}

	/**
	 * dataProvider
	 */
	public function SmileyDataProvider()
	{
		return [
			[
				'Hi :-)',
				'Hi <img src="https://youthweb.net/vendor/smilies/smile0001.gif" alt=":-)" title=":-)" />',
			],
			[
				'Hey :super: Das war sehr gut.',
				'Hey <img src="https://youthweb.net/vendor/smilies/489.gif" alt=":super:" title=":super:" /> Das war sehr gut.',
			],
		];
	}
}
