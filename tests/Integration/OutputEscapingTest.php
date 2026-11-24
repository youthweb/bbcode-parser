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

use Youthweb\BBCodeParser\Manager;

/**
 * The options and the content of tags must not reach the output as markup or script.
 */
class OutputEscapingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider quoteProvider
     *
     * @param mixed $text
     * @param mixed $expected
     */
    public function testOptionOfAQuoteIsEscaped($text, $expected)
    {
        $this->assertSame($expected, (new Manager())->parse($text));
    }

    public function quoteProvider()
    {
        return [
            'quote' => [
                '[quote=<svg/onload=alert(1)>]x[/quote]',
                '<p><blockquote title="Zitat"><cite>&lt;svg/onload=alert(1)&gt;</cite>x</blockquote></p>',
            ],
            'q' => [
                '[q=<svg/onload=alert(1)>]x[/q]',
                '<p><blockquote title="Zitat"><cite>&lt;svg/onload=alert(1)&gt;</cite>x</blockquote></p>',
            ],
            'Z' => [
                '[Z=<svg/onload=alert(1)>]x[/Z]',
                '<p><blockquote title="Zitat"><cite>&lt;svg/onload=alert(1)&gt;</cite>x</blockquote></p>',
            ],
            'quoted option with quotes' => [
                '[quote="a&quot;b"]x[/quote]',
                '<p><blockquote title="Zitat"><cite>a&quot;b</cite>x</blockquote></p>',
            ],
            'ampersand' => [
                '[quote="Tom & Jerry"]x[/quote]',
                '<p><blockquote title="Zitat"><cite>Tom &amp; Jerry</cite>x</blockquote></p>',
            ],
            'plain name stays' => [
                '[quote="Albert Einstein"]x[/quote]',
                '<p><blockquote title="Zitat"><cite>Albert Einstein</cite>x</blockquote></p>',
            ],
        ];
    }

    /**
     * @dataProvider videoProvider
     *
     * @param mixed $text
     * @param mixed $expected
     */
    public function testContentOfAVideoIsEscaped($text, $expected)
    {
        $this->assertSame($expected, (new Manager())->parse($text));
    }

    public function videoProvider()
    {
        return [
            'v, no url' => [
                '[v]<svg/onload=alert(1)>[/v]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'video, no url' => [
                '[video]<svg/onload=alert(1)>[/video]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'youtube, no url' => [
                '[youtube]<svg/onload=alert(1)>[/youtube]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'url with markup' => [
                '[v]http://example.org/<svg/onload=alert(1)>[/v]',
                '<p><a target="_blank" href="http://example.org/&lt;svg/onload=alert(1)&gt;">http://example.org/&lt;svg/onload=alert(1)&gt;</a></p>',
            ],
            'plain url is linked' => [
                '[v]http://example.org/video[/v]',
                '<p><a target="_blank" href="http://example.org/video">http://example.org/video</a></p>',
            ],
            'url with ampersand' => [
                '[v]http://example.org/video?a=1&b=2[/v]',
                '<p><a target="_blank" href="http://example.org/video?a=1&amp;b=2">http://example.org/video?a=1&b=2</a></p>',
            ],
        ];
    }

    /**
     * @dataProvider imageProvider
     *
     * @param mixed $text
     * @param mixed $expected
     */
    public function testContentOfAnImageIsEscaped($text, $expected)
    {
        $html = (new Manager())->parse($text, ['callbacks' => ['image' => ['force_check' => false]]]);

        $this->assertSame($expected, $html);
    }

    public function imageProvider()
    {
        return [
            'img, no url' => [
                '[img]<svg/onload=alert(1)>[/img]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'pic, no url' => [
                '[pic]<svg/onload=alert(1)>[/pic]',
                '<p>&lt;svg/onload=alert(1)&gt;</p>',
            ],
            'img, quote in the url' => [
                '[img]http://example.org/x.png" onerror="alert(1)[/img]',
                '<p>http://example.org/x.png&quot; onerror=&quot;alert(1)</p>',
            ],
            'img in a url tag, no url' => [
                '[url=http://example.org][img]<svg/onload=alert(1)>[/img][/url]',
                '<p><a target="_blank" href="http://example.org">&lt;svg/onload=alert(1)&gt;</a></p>',
            ],
            'plain image stays' => [
                '[img]http://example.org/x.png[/img]',
                '<p><a target="_blank" href="http://example.org/x.png"><img class="img-responsive" border="0" src="http://example.org/x.png" alt="x" /></a></p>',
            ],
        ];
    }

    /**
     * @dataProvider emailProvider
     *
     * @param mixed $text
     * @param mixed $contained
     * @param mixed $notContained
     */
    public function testTextOfAProtectedEmailStaysInsideTheScriptString($text, $contained, $notContained)
    {
        $html = (new Manager())->parse($text, ['callbacks' => ['email_content' => ['protect_email' => true]]]);

        $this->assertStringContainsString($contained, $html);

        if ($notContained !== null) {
            $this->assertStringNotContainsString($notContained, $html);
        }

        // Only the own script element is in the output, and it is closed once.
        $this->assertSame(1, substr_count(strtolower($html), '<script'));
        $this->assertSame(1, substr_count(strtolower($html), '</script>'));
    }

    public function emailProvider()
    {
        return [
            'quote ends the string' => [
                "[email=a@b.de]x');alert(1);//[/email]",
                "x\\u0027);alert(1);//</a>",
                "x');alert(1)",
            ],
            'script end tag' => [
                '[email=a@b.de]</script><b>x[/email]',
                "&lt;/script&gt;&lt;b&gt;x</a>",
                null,
            ],
            'backslash' => [
                '[email=a@b.de]x\\[/email]',
                "x\\\\</a>",
                null,
            ],
            'plain text stays' => [
                '[email=a@b.de]Mail me[/email]',
                '>Mail me</a>',
                null,
            ],
        ];
    }
}
