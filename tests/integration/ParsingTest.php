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
	/**
	 * @dataProvider UrlDataProvider
	 */
	public function testParseUrls($text, $expected)
	{
		$parser = new Manager();

		$this->assertSame($expected, $parser->parse($text));
	}

	/**
	 * @HeaderDataProvider
	 */
	public function UrlDataProvider()
	{
		return [
			[
				'example.org',
				'<p><a href="http://example.org">example.org</a></p>',
			],
			[
				'Mehr Infos gibt es auf http://example.org/pfad?query=string',
				'<p>Mehr Infos gibt es auf <a href="http://example.org/pfad?query=string">example.org/pfad</a></p>',
			],
			[
				'Quellen:
[list]
[*] http://example.org
[*] https://example.org/test
[*]https://example.org/ohne_leerzeichen_am_Anfang
[*] Siehe 3. Absatz auf https://example.org/pfad?query=string
[/list]',
				'<p>Quellen:</p>
<ul type="disc">
'."\t".'<li><a href="http://example.org">example.org</a></li>
'."\t".'<li><a href="https://example.org/test">example.org/test</a></li>
'."\t".'<li><a href="https://example.org/ohne_leerzeichen_am_Anfang">example.org/ohne_leerzeichen_am_Anfang</a></li>
'."\t".'<li>Siehe 3. Absatz auf <a href="https://example.org/pfad?query=string">example.org/pfad</a></li>
</ul>',
			],
		];
	}

	/**
	 * Ermittelt den EOL-Typ für Debug-Zwecke
	 *
	 * @see https://stackoverflow.com/a/24927253
	 */
	private function detectEOL($str)
	{
		$eols = array(
			'lfcr' => "\n\r", // 0x0A - 0x0D - acorn BBC
			'crlf' => "\r\n", // 0x0D - 0x0A - Windows, DOS OS/2
			'lf'   => "\n",   // 0x0A -      - Unix, OSX
			'cr'   => "\r",   // 0x0D -      - Apple ][, TRS80
		);

		$key = "";
		$curCount = 0;
		$curEol = '';

		foreach($eols as $k => $eol)
		{
			if ( ($count = substr_count($str, $eol)) > $curCount )
			{
				$curCount = $count;
				$curEol = $eol;
				$key = $k;
			}
		}

		//return $curEol;
		return $key;
	}

}
