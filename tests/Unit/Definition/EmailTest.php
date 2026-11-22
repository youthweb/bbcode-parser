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

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Email;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Email::class)]
class EmailTest extends \PHPUnit\Framework\TestCase
{
    use MockerTrait;

    #[DataProvider('dataProvider')]
    public function testAsHtml(string $text, null $attribute, string $expected): void
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->getMockBuilder(\Youthweb\BBCodeParser\Config::class)
            ->onlyMethods(['get'])
            ->getMock();

        $config->method('get')
            ->willReturnMap(
                [
                    ['cacheitempool', null, null],
                    ['callbacks.email_content.protect_email', null, false],
                ],
            );

        $definition = new Email($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    #[DataProvider('dataProviderProtectedEmail')]
    public function testAsHtmlWithProtectedEmail(string $text, null $attribute, string $expected): void
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->getMockBuilder(\Youthweb\BBCodeParser\Config::class)
            ->onlyMethods(['get'])
            ->getMock();

        $config->method('get')
            ->willReturnMap(
                [
                    ['cacheitempool', null, null],
                    ['callbacks.email_content.protect_email', null, true],
                ],
            );

        $definition = new Email($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public static function dataProvider()
    {
        return [
            [
                'mail@example.org',
                null,
                '<a href="mailto:mail@example.org">mail@example.org</a>',
            ],
            [
                'invalid email',
                null,
                'invalid email',
            ],
            [
                '',
                null,
                '',
            ],
        ];
    }

    /**
     * data provider for protected emails
     */
    public static function dataProviderProtectedEmail()
    {
        return [
            [
                'mail@example.org',
                null,
                '<script type="text/javascript">(function() {var user = "mail";var at = "@";var server = "example.org";document.write(\'<a href="\' + \'mail\' + \'to:\' + user + at + server + \'">mail@example.org</a>\');})();</script>',
            ],
            [
                'invalid email',
                null,
                'invalid email',
            ],
        ];
    }
}
