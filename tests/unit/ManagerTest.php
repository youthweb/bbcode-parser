<?php

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Manager;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function testParseWithoutDefinitions()
	{
		$manager = new Manager();

		$config = [
			'parse_headlines' => false,
			'parse_default' => false,
		];

		$text = '[b]test[/b]';

		$this->assertSame($text, $manager->parse($text, $config));
	}
}
