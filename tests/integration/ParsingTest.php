<?php

namespace Youthweb\BBCodeParser\Tests\Integration;

use Youthweb\BBCodeParser\Manager;

class ParsingTest extends \PHPUnit_Framework_TestCase
{

	private $parser;

	/**
	 * @dataProvider providerParseBBCode
	 */
	public function setUp()
	{
		$this->parser = new Manager();
	}

	/**
	 * @dataProvider providerParseBBCode
	 */
	public function testParseBBCode($text, array $config, $expected)
	{
		$this->assertSame($expected, $this->parser->parse($text, $config));
	}

	public function providerParseBBCode()
	{
		return [
			[
				'[b]Hello World![/b]',
				[],
				'<p><b>Hello World!</b></p>',
			],
			[
				'[i]Hello World![/i]',
				[],
				'<p><i>Hello World!</i></p>',
			],
			[
				'[u]Hello World![/u]',
				[],
				'<p><u>Hello World!</u></p>',
			],
			// Headlines
			[
				'[h1]Header 1[/h1]',
				[
					'parse_headlines' => true,
				],
				'<h1>Header 1</h1>',
			],
			[
				'[h2]Header 2[/h2]',
				[
					'parse_headlines' => true,
				],
				'<h2>Header 2</h2>',
			],
			[
				'[h3]Header 3[/h3]',
				[
					'parse_headlines' => true,
				],
				'<h3>Header 3</h3>',
			],
			[
				'[h4]Header 4[/h4]',
				[
					'parse_headlines' => true,
				],
				'<h4>Header 4</h4>',
			],
			[
				'[h5]Header 5[/h5]',
				[
					'parse_headlines' => true,
				],
				'<h5>Header 5</h5>',
			],
			[
				'[h6]Header 6[/h6]',
				[
					'parse_headlines' => true,
				],
				'<h6>Header 6</h6>',
			],
			[
				'[h7]Header 7[/h7]',
				[
					'parse_headlines' => true,
				],
				'<p>[h7]Header 7[/h7]</p>',
			],
			// Code
			[
				'[code]<h7>Header 7</h7>[/code]',
				[],
				'<p><code>&lt;h7&gt;Header 7&lt;/h7&gt;</code></p>',
			],
			[
				'[noparse]<h7>Header 7</h7>[/noparse]',
				[],
				'<p>&lt;h7&gt;Header 7&lt;/h7&gt;</p>',
			],
			// Urls
			[
				'example.org',
				[],
				'<p><a href="http://example.org">example.org</a></p>',
			],
			[
				'[url]http://www.example.com/irgend/eine/lange/url/die/gek%C3%BCrzt/werden/soll.html[/url]',
				[],
				'<p><a href="http://www.example.com/irgend/eine/lange/url/die/gek%C3%BCrzt/werden/soll.html">www.example.com/irgend/eine/lange/url/die/gek%C3%BCrzt/werden/soll.html</a></p>',
			],
			[
				'Mehr Infos gibt es auf http://example.org/pfad?query=string',
				[],
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
				[],
				'<p>Quellen:</p>
<ul type="disc">
'."\t".'<li><a href="http://example.org">example.org</a></li>
'."\t".'<li><a href="https://example.org/test">example.org/test</a></li>
'."\t".'<li><a href="https://example.org/ohne_leerzeichen_am_Anfang">example.org/ohne_leerzeichen_am_Anfang</a></li>
'."\t".'<li>Siehe 3. Absatz auf <a href="https://example.org/pfad?query=string">example.org/pfad</a></li>
</ul>',
			],
			[
				'Here\'s an e-mail-address:bob+test@example.org. Here\'s an authenticated URL: http://skroob:12345@example.com.',
				[],
				'<p>Here\'s an e-mail-address:<a href="mailto:bob+test&#64;example.org">bob+test&#64;example.org</a>. Here\'s an authenticated URL: <a href="http://skroob:12345&#64;example.com">example.com</a>.</p>',
			],
			[
				'Here are some URLs:
stackoverflow.com/questions/1188129/pregreplace-to-detect-html-php
Here\'s the answer: http://www.google.com/search?rls=en&q=42&ie=utf-8&oe=utf-8&hl=en. What was the question?
A quick look at \'http://en.wikipedia.org/wiki/URI_scheme#Generic_syntax\' is helpful.',
				[],
				'<p>Here are some URLs:<br />
<a href="http://stackoverflow.com/questions/1188129/pregreplace-to-detect-html-php">stackoverflow.com/questions/1188129/pregreplace-to-detect-html-php</a><br />
Here\'s the answer: <a href="http://www.google.com/search?rls=en&amp;q=42&amp;ie=utf-8&amp;oe=utf-8&amp;hl=en">www.google.com/search</a>. What was the question?<br />
A quick look at \'<a href="http://en.wikipedia.org/wiki/URI_scheme#Generic_syntax">en.wikipedia.org/wiki/URI_scheme</a>\' is helpful.</p>',
			],
			[
				'There is no place like 127.0.0.1! Except maybe http://news.bbc.co.uk/1/hi/england/surrey/8168892.stm?
Ports: 192.168.0.1:8080, https://example.net:1234/.
Beware of Greeks bringing internationalized top-level domains (xn--hxajbheg2az3al.xn--jxalpdlp).
10.000.000.000 is not an IP-address. Nor is this.a.domain.',
				[],
				'<p>There is no place like <a href="http://127.0.0.1">127.0.0.1</a>! Except maybe <a href="http://news.bbc.co.uk/1/hi/england/surrey/8168892.stm">news.bbc.co.uk/1/hi/england/surrey/8168892.stm</a>?<br />
Ports: <a href="http://192.168.0.1:8080">192.168.0.1:8080</a>, <a href="https://example.net:1234/">example.net:1234/</a>.<br />
Beware of Greeks bringing internationalized top-level domains (xn--hxajbheg2az3al.xn--jxalpdlp).<br />
10.000.000.000 is not an IP-address. Nor is this.a.domain.</p>',
			],
			// HTML
			[
				'<script>alert(\'Remember kids: Say no to XSS-attacks! Always HTML escape untrusted input!\');</script>',
				[],
				'<p>&lt;script&gt;alert(\'Remember kids: Say no to XSS-attacks! Always HTML escape untrusted input!\');&lt;/script&gt;</p>',
			],
			[
				'<p>Der p-Tag muss escaped werden</p>',
				[],
				'<p>&lt;p&gt;Der p-Tag muss escaped werden&lt;/p&gt;</p>',
			],
			[
				'<a href="https://example.com">Der a-Tag muss escaped werden.</a>',
				[],
				'<p>&lt;a href=&quot;<a href="https://example.com">example.com</a>&quot;&gt;Der a-Tag muss escaped werden.&lt;/a&gt;</p>',
			],
			[
				'https://mail.google.com/mail/u/0/#starred?compose=141d598cd6e13025
https://www.google.com/search?q=bla%20bla%20bla
https://www.google.com/search?q=bla+bla+bla',
				[],
				'<p><a href="https://mail.google.com/mail/u/0/#starred?compose=141d598cd6e13025">mail.google.com/mail/u/0/</a><br />
<a href="https://www.google.com/search?q=bla%20bla%20bla">www.google.com/search</a><br />
<a href="https://www.google.com/search?q=bla+bla+bla">www.google.com/search</a></p>',
			],
			[
				'We need to support IDNs and IRIs and röck döts:
møøse.kwi.dk/阿驼鹿一旦咬了我的妹妹/من-اليمين-إلى-اليسار-لغات-تخلط-لي.',
				[],
				'<p>We need to support IDNs and IRIs and röck döts:<br />
<a href="http://møøse.kwi.dk/阿驼鹿一旦咬了我的妹妹/من-اليمين-إلى-اليسار-لغات-تخلط-لي">møøse.kwi.dk/阿驼鹿一旦咬了我的妹妹/من-اليمين-إلى-اليسار-لغات-تخلط-لي</a>.</p>',
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
