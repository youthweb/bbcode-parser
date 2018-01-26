<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Email;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class EmailTest extends \PHPUnit\Framework\TestCase
{
    use MockerTrait;

    /**
     * @dataProvider dataProvider
     *
     * @param mixed $text
     * @param mixed $attribute
     * @param mixed $expected
     */
    public function testAsHtml($text, $attribute, $expected)
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
            ->setMethods(['get'])
            ->getMock();

        $config->method('get')
            ->will(
                $this->returnValueMap(
                    [
                        ['cacheitempool', null, null],
                        ['callbacks.email_content.protect_email', null, false],
                    ]
                )
            );

        $definition = new Email($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * @dataProvider dataProviderProtectedEmail
     *
     * @param mixed $text
     * @param mixed $attribute
     * @param mixed $expected
     */
    public function testAsHtmlWithProtectedEmail($text, $attribute, $expected)
    {
        $elementNode = $this->buildElementNodeMock($text, $attribute);

        $config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
            ->setMethods(['get'])
            ->getMock();

        $config->method('get')
            ->will(
                $this->returnValueMap(
                    [
                        ['cacheitempool', null, null],
                        ['callbacks.email_content.protect_email', null, true],
                    ]
                )
            );

        $definition = new Email($config);

        $this->assertSame($expected, $definition->asHtml($elementNode));
    }

    /**
     * data provider
     */
    public function dataProvider()
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
    public function dataProviderProtectedEmail()
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
