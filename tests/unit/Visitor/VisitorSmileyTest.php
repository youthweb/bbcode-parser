<?php

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Visitor\VisitorSmiley;

class VisitorSmileyTest extends \PHPUnit_Framework_TestCase
{
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
