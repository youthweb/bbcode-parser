<?php

namespace Youthweb\BBCodeParser\Tests\Integration;

use Youthweb\BBCodeParser\Manager;

class ParsingTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function testParseBCode()
	{
		$text     = '[b]Hello World![/b]';
		$expected = '<p><b>Hello World!</b></p>';

		$parser = new Manager();

		$this->assertSame($parser->parse($text), $expected);
	}

	/**
	 * @test
	 */
	public function testParseICode()
	{
		$text     = '[i]Hello World![/i]';
		$expected = '<p><i>Hello World!</i></p>';

		$parser = new Manager();

		$this->assertSame($parser->parse($text), $expected);
	}

	/**
	 * @test
	 */
	public function testParseUCode()
	{
		$text     = '[u]Hello World![/u]';
		$expected = '<p><u>Hello World!</u></p>';

		$parser = new Manager();

		$this->assertSame($parser->parse($text), $expected);
	}

	/**
	 * @dataProvider HeaderDataProvider
	 */
	public function testParseHeaderCode($text, $expected)
	{
		$config = [
			'parse_headlines' => true,
		];

		$parser = new Manager();

		$this->assertSame($expected, $parser->parse($text, $config));
	}

	/**
	 * @HeaderDataProvider
	 */
	public function HeaderDataProvider()
	{
		return [
			[
				'[h1]Header 1[/h1]',
				'<h1>Header 1</h1>',
			],
			[
				'[h2]Header 2[/h2]',
				'<h2>Header 2</h2>',
			],
			[
				'[h3]Header 3[/h3]',
				'<h3>Header 3</h3>',
			],
			[
				'[h4]Header 4[/h4]',
				'<h4>Header 4</h4>',
			],
			[
				'[h5]Header 5[/h5]',
				'<h5>Header 5</h5>',
			],
			[
				'[h6]Header 6[/h6]',
				'<h6>Header 6</h6>',
			],
			[
				'[h7]Header 7[/h7]',
				'<p>[h7]Header 7[/h7]</p>',
			],
		];
	}


}
