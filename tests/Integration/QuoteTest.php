<?php

namespace Youthweb\BBCodeParser\Tests\Integration;

use Youthweb\BBCodeParser\Manager;

class QuoteTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @test
	 */
	public function testParseQutoeCode()
	{
		$text     = '[q="Albert Einstein"]*bäh*[/q]';
		$expected = '<p><blockquote title="Zitat"><cite>Albert Einstein</cite>*bäh*</blockquote></p>';

		$parser = new Manager();

		$this->assertSame($parser->parse($text), $expected);
	}

}
