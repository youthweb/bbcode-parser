<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Definition;

use JBBCode\ElementNode;
use JBBCode\TextNode;
use Youthweb\BBCodeParser\Definition\Email;
use Youthweb\BBCodeParser\Tests\Fixtures\MockerTrait;

class EmailTest extends \PHPUnit_Framework_TestCase
{
	use MockerTrait;

	/**
	 * @dataProvider dataProvider
	 */
	public function testAsHtml($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->method('get')
			->with('callbacks.email_content.protect_email')
			->willReturn(false);

		$definition = new Email($config);

		$this->assertSame($expected, $definition->asHtml($elementNode));
	}

	/**
	 * @dataProvider dataProviderProtectedEmail
	 */
	public function testAsHtmlWithProtectedEmail($text, $attribute, $expected)
	{
		$elementNode = $this->buildElementNodeMock($text, $attribute);

		$config = $this->getMockBuilder('Youthweb\BBCodeParser\Config')
			->setMethods(['get'])
			->getMock();

		$config->method('get')
			->with('callbacks.email_content.protect_email')
			->willReturn(true);

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
