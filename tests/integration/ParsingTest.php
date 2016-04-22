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
}
