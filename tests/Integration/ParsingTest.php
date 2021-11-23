<?php
/*
 * A BBCode-to-HTML parser for youthweb.net
 * Copyright (C) 2016-2021  Youthweb e.V. <info@youthweb.net>

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Youthweb\BBCodeParser\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Manager;

class ParsingTest extends TestCase
{
    private $parser;

    /**
     * @dataProvider providerParseBBCode
     */
    public function setUp(): void
    {
        $this->parser = new Manager();
    }

    /**
     * @dataProvider providerParseBBCode
     *
     * @param mixed $text
     * @param mixed $expected
     */
    public function testParseBBCode($text, array $config, $expected)
    {
        $this->assertSame($expected, $this->parser->parse($text, $config));
    }

    public function providerParseBBCode()
    {
        return [
            [
                '[b]Hello World! <img src="javascript:alert(\'XSS\')">[/b]',
                [],
                '<p><b>Hello World! &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</b></p>',
            ],
            [
                '[i]Hello World! <img src="javascript:alert(\'XSS\')">[/i]',
                [],
                '<p><i>Hello World! &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</i></p>',
            ],
            [
                '[u]Hello World! <img src="javascript:alert(\'XSS\')">[/u]',
                [],
                '<p><u>Hello World! &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</u></p>',
            ],
            // Headlines
            [
                '[h1]Header 1 <img src="javascript:alert(\'XSS\')">[/h1]',
                [
                    'parse_headlines' => true,
                ],
                '<h1>Header 1 &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</h1>',
            ],
            [
                '[h2]Header 2 <img src="javascript:alert(\'XSS\')">[/h2]',
                [
                    'parse_headlines' => true,
                ],
                '<h2>Header 2 &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</h2>',
            ],
            [
                '[h3]Header 3 <img src="javascript:alert(\'XSS\')">[/h3]',
                [
                    'parse_headlines' => true,
                ],
                '<h3>Header 3 &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</h3>',
            ],
            [
                '[h4]Header 4 <img src="javascript:alert(\'XSS\')">[/h4]',
                [
                    'parse_headlines' => true,
                ],
                '<h4>Header 4 &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</h4>',
            ],
            [
                '[h5]Header 5 <img src="javascript:alert(\'XSS\')">[/h5]',
                [
                    'parse_headlines' => true,
                ],
                '<h5>Header 5 &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</h5>',
            ],
            [
                '[h6]Header 6 <img src="javascript:alert(\'XSS\')">[/h6]',
                [
                    'parse_headlines' => true,
                ],
                '<h6>Header 6 &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</h6>',
            ],
            [
                '[h7]Header 7 <img src="javascript:alert(\'XSS\')">[/h7]',
                [
                    'parse_headlines' => true,
                ],
                '<p>[h7]Header 7 &lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;[/h7]</p>',
            ],
            // Code
            [
                '[code]<h7>Header 7</h7>[/code]',
                [],
                '<pre><code>&lt;h7&gt;Header 7&lt;/h7&gt;</code></pre>',
            ],
            // Do not double encode
            [
                '[code]&lt;h7&gt;Header 7&lt;/h7&gt;[/code]',
                [],
                '<pre><code>&lt;h7&gt;Header 7&lt;/h7&gt;</code></pre>',
            ],
            [
                '[code]
Durchmesser der Erde:    D  = 12742 km = 12742000 m
Umfang der Erde:         U  = Pi*D     = 40030173,592 m
Seillängenge:            S  = U+1      = 40030174,592 m
Seil-Durchmesser:        SD = S/Pi     = 12742000,318 m
Abstand Seil zu Boden:   l  = (SD-D)/2 = 0,159m
<img src="javascript:alert(\'XSS\')">
[/code]',
                [],
                '<pre><code>
Durchmesser der Erde:    D  = 12742 km = 12742000 m
Umfang der Erde:         U  = Pi*D     = 40030173,592 m
Seillängenge:            S  = U+1      = 40030174,592 m
Seil-Durchmesser:        SD = S/Pi     = 12742000,318 m
Abstand Seil zu Boden:   l  = (SD-D)/2 = 0,159m
&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;
</code></pre>'
            ],
            [
                'Hier ein Beispiel:

[code]
<?php
    $message = \'Hello World!\';
    echo $message;
[/code]',
                [],
                '<p>Hier ein Beispiel:</p>
<pre><code>
&lt;?php
    $message = \'Hello World!\';
    echo $message;
</code></pre>'
            ],
            [
                'Hier ein Beispiel mit [code]Code-Beispiel[/code] in einer Zeile.',
                [],
                '<p>Hier ein Beispiel mit</p>
<pre><code>Code-Beispiel</code></pre>
<p>in einer Zeile.</p>'
            ],
            [
                '[noparse]<h7>Header 7</h7>[/noparse]',
                [],
                '<p>&lt;h7&gt;Header 7&lt;/h7&gt;</p>',
            ],
            // Do not double encode
            [
                '[noparse]&lt;h7&gt;Header 7&lt;/h7&gt;[/noparse]',
                [],
                '<p>&lt;h7&gt;Header 7&lt;/h7&gt;</p>',
            ],
            // Images callbacks.image.force_check
            [
                '[img]http://example.org/image.jpg[/img]',
                [
                    'callbacks' => [
                        'image' => [
                            'force_check' => false,
                        ],
                    ],
                ],
                '<p><a target="_blank" href="http://example.org/image.jpg"><img class="img-responsive" border="0" src="http://example.org/image.jpg" alt="image" /></a></p>',
            ],
            // Urls
            [
                'example.org',
                [],
                '<p><a target="_blank" href="http://example.org">http://example.org</a></p>',
            ],
            [
                '[url]http://www.example.com/irgend/eine/lange/url/die/gek%C3%BCrzt/werden/soll.html[/url]',
                [],
                '<p><a target="_blank" href="http://www.example.com/irgend/eine/lange/url/die/gek%C3%BCrzt/werden/soll.html">http://www.example.com/irgend/eine/lange/url/die/gek%C3%BCrzt/werden/soll.html</a></p>',
            ],
            [
                'Mehr Infos gibt es auf http://example.org/pfad?query=string',
                [],
                '<p>Mehr Infos gibt es auf <a target="_blank" href="http://example.org/pfad?query=string">http://example.org/pfad?query=string</a></p>',
            ],
            [
                'Jemand hat dich eingeladen, am Event [url=http://example.org/events/6]"E. "><img src=javascript:alert(\'XSS\')>"[/url] teilzunehmen.',
                [],
                '<p>Jemand hat dich eingeladen, am Event <a target="_blank" href="http://example.org/events/6">&quot;E. &quot;&gt;&lt;img src=javascript:alert(\'XSS\')&gt;&quot;</a> teilzunehmen.</p>',
            ],
            [
                'B1: Jemand hat dich eingeladen, am Event [url=http://example.org/events/6][b]"E. "><img src=javascript:alert(\'XSS\')>"[/b][/url] teilzunehmen.',
                [],
                '<p>B1: Jemand hat dich eingeladen, am Event <a target="_blank" href="http://example.org/events/6"><b>&quot;E. &quot;&gt;&lt;img src=javascript:alert(\'XSS\')&gt;&quot;</b></a> teilzunehmen.</p>',
            ],
            [
                'B2: Jemand hat dich eingeladen, am Event [url=http://example.org/events/6][F]"E. "><img src=javascript:alert(\'XSS\')>"[/F][/url] teilzunehmen.',
                [],
                '<p>B2: Jemand hat dich eingeladen, am Event <a target="_blank" href="http://example.org/events/6"><b>&quot;E. &quot;&gt;&lt;img src=javascript:alert(\'XSS\')&gt;&quot;</b></a> teilzunehmen.</p>',
            ],
            [
                'I1: Jemand hat dich eingeladen, am Event [url=http://example.org/events/6][i]"E. "><img src="javascript:alert(\'XSS\')">"[/i][/url] teilzunehmen.',
                [],
                '<p>I1: Jemand hat dich eingeladen, am Event <a target="_blank" href="http://example.org/events/6"><i>&quot;E. &quot;&gt;&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;&quot;</i></a> teilzunehmen.</p>',
            ],
            [
                'I2: Jemand hat dich eingeladen, am Event [url=http://example.org/events/6][K]"E. "><img src="javascript:alert(\'XSS\')">"[/K][/url] teilzunehmen.',
                [],
                '<p>I2: Jemand hat dich eingeladen, am Event <a target="_blank" href="http://example.org/events/6"><i>&quot;E. &quot;&gt;&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;&quot;</i></a> teilzunehmen.</p>',
            ],
            [
                'U1: Jemand hat dich eingeladen, am Event [url=http://example.org/events/6][u]"E. "><img src="javascript:alert(\'XSS\')">"[/u][/url] teilzunehmen.',
                [],
                '<p>U1: Jemand hat dich eingeladen, am Event <a target="_blank" href="http://example.org/events/6"><u>&quot;E. &quot;&gt;&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;&quot;</u></a> teilzunehmen.</p>',
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
' . "\t" . '<li><a target="_blank" href="http://example.org">http://example.org</a></li>
' . "\t" . '<li><a target="_blank" href="https://example.org/test">https://example.org/test</a></li>
' . "\t" . '<li><a target="_blank" href="https://example.org/ohne_leerzeichen_am_Anfang">https://example.org/ohne_leerzeichen_am_Anfang</a></li>
' . "\t" . '<li>Siehe 3. Absatz auf <a target="_blank" href="https://example.org/pfad?query=string">https://example.org/pfad?query=string</a></li>
</ul>',
            ],
            [
                'Mögliche Angriffe:
[list]
[*] <img src="javascript:alert(\'XSS\')">
[*] [b]<img src="javascript:alert(\'XSS\')">[/b]
[*] [b][i]<img src="javascript:alert(\'XSS\')">[/i][/b]
[*] [b][i][u]<img src="javascript:alert(\'XSS\')">[/u][/i][/b]
[/list]',
                [],
                '<p>Mögliche Angriffe:</p>
<ul type="disc">
' . "\t" . '<li>&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</li>
' . "\t" . '<li><b>&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</b></li>
' . "\t" . '<li><b><i>&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</i></b></li>
' . "\t" . '<li><b><i><u>&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</u></i></b></li>
</ul>',
            ],
            [
                'Mögliche Angriffe:
[list]
[*] <img src="javascript:alert(\'XSS\')">
[*] [F]<img src="javascript:alert(\'XSS\')">[/F]
[*] [F][K]<img src="javascript:alert(\'XSS\')">[/K][/F]
[*] [F][K][u]<img src="javascript:alert(\'XSS\')">[/u][/K][/F]
[/list]',
                [],
                '<p>Mögliche Angriffe:</p>
<ul type="disc">
' . "\t" . '<li>&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</li>
' . "\t" . '<li><b>&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</b></li>
' . "\t" . '<li><b><i>&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</i></b></li>
' . "\t" . '<li><b><i><u>&lt;img src=&quot;javascript:alert(\'XSS\')&quot;&gt;</u></i></b></li>
</ul>',
            ],
            [
                'mail@example.com',
                [],
                '<p>mail@example.com</p>',
            ],
            [
                '[email]mail@example.com[/email]',
                [],
                '<p><a href="mailto:mail@example.com">mail@example.com</a></p>',
            ],
            [
                'Here\'s an e-mail-address:bob+test@example.org. Here\'s an authenticated URL: http://skroob:12345@example.com.',
                [],
                '<p>Here\'s an e-mail-address:bob+test@example.org. Here\'s an authenticated URL: <a target="_blank" href="http://skroob:12345@example.com">http://skroob:12345@example.com</a>.</p>',
            ],
            [
                'Here are some URLs:
stackoverflow.com/questions/1188129/pregreplace-to-detect-html-php
Here\'s the answer: http://www.google.com/search?rls=en&q=42&ie=utf-8&oe=utf-8&hl=en. What was the question?
A quick look at \'http://en.wikipedia.org/wiki/URI_scheme#Generic_syntax\' is helpful.',
                [],
                '<p>Here are some URLs:<br />
<a target="_blank" href="http://stackoverflow.com/questions/1188129/pregreplace-to-detect-html-php">http://stackoverflow.com/questions/1188129/pregreplace-to-detect-html-php</a><br />
Here\'s the answer: <a target="_blank" href="http://www.google.com/search?rls=en&amp;q=42&amp;ie=utf-8&amp;oe=utf-8&amp;hl=en">http://www.google.com/search?rls=en&q=42&ie=utf-8&oe=utf-8&hl=en</a>. What was the question?<br />
A quick look at \'<a target="_blank" href="http://en.wikipedia.org/wiki/URI_scheme#Generic_syntax">http://en.wikipedia.org/wiki/URI_scheme#Generic_syntax</a>\' is helpful.</p>',
            ],
            [
                'There is no place like 127.0.0.1! Except maybe http://news.bbc.co.uk/1/hi/england/surrey/8168892.stm?
Ports: 192.168.0.1:8080, https://example.net:1234/.
Beware of Greeks bringing internationalized top-level domains (xn--hxajbheg2az3al.xn--jxalpdlp).
10.000.000.000 is not an IP-address. Nor is this.a.domain.',
                [],
                '<p>There is no place like <a target="_blank" href="http://127.0.0.1">http://127.0.0.1</a>! Except maybe <a target="_blank" href="http://news.bbc.co.uk/1/hi/england/surrey/8168892.stm">http://news.bbc.co.uk/1/hi/england/surrey/8168892.stm</a>?<br />
Ports: <a target="_blank" href="http://192.168.0.1:8080">http://192.168.0.1:8080</a>, <a target="_blank" href="https://example.net:1234">https://example.net:1234/</a>.<br />
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
                '<p>&lt;a href=&quot;<a target="_blank" href="https://example.com">https://example.com</a>&quot;&gt;Der a-Tag muss escaped werden.&lt;/a&gt;</p>',
            ],
            [
                'https://mail.google.com/mail/u/0/#starred?compose=141d598cd6e13025
https://www.google.com/search?q=bla%20bla%20bla
https://www.google.com/search?q=bla+bla+bla',
                [],
                '<p><a target="_blank" href="https://mail.google.com/mail/u/0/#starred?compose=141d598cd6e13025">https://mail.google.com/mail/u/0/#starred?compose=141d598cd6e13025</a><br />
<a target="_blank" href="https://www.google.com/search?q=bla%20bla%20bla">https://www.google.com/search?q=bla%20bla%20bla</a><br />
<a target="_blank" href="https://www.google.com/search?q=bla+bla+bla">https://www.google.com/search?q=bla+bla+bla</a></p>',
            ],
            [
                'We need to support IDNs and IRIs and röck döts:
møøse.kwi.dk/阿驼鹿一旦咬了我的妹妹/من-اليمين-إلى-اليسار-لغات-تخلط-لي.',
                [],
                '<p>We need to support IDNs and IRIs and röck döts:<br />
<a target="_blank" href="http://møøse.kwi.dk/阿驼鹿一旦咬了我的妹妹/من-اليمين-إلى-اليسار-لغات-تخلط-لي">http://møøse.kwi.dk/阿驼鹿一旦咬了我的妹妹/من-اليمين-إلى-اليسار-لغات-تخلط-لي</a>.</p>',
            ],
        ];
    }

    /**
     * Ermittelt den EOL-Typ für Debug-Zwecke
     *
     * @see https://stackoverflow.com/a/24927253
     *
     * @param mixed $str
     */
    private function detectEOL($str)
    {
        $eols = [
            'lfcr' => "\n\r", // 0x0A - 0x0D - acorn BBC
            'crlf' => "\r\n", // 0x0D - 0x0A - Windows, DOS OS/2
            'lf'   => "\n",   // 0x0A -      - Unix, OSX
            'cr'   => "\r",   // 0x0D -      - Apple ][, TRS80
        ];

        $key = '';
        $curCount = 0;
        $curEol = '';

        foreach ($eols as $k => $eol) {
            if (($count = substr_count($str, $eol)) > $curCount) {
                $curCount = $count;
                $curEol = $eol;
                $key = $k;
            }
        }

        return $key;
    }
}
