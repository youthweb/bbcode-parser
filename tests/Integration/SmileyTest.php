<?php

namespace Youthweb\BBCodeParser\Tests\Integration;

use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Visitor\VisitorSmiley;

class SmileyTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @test
	 */
	public function testParseSmileyCode()
	{
		$text     = 'My mistake :-[';
		$expected = '<p>My mistake <img src="https://youthweb.net/vendor/smilies/49_2.gif" alt=":-[" title=":-[" /></p>';

		$parser = new Manager();

		$config = [
			'visitor' => [
				'smiley' => new VisitorSmiley()
			]
		];

		$this->assertSame($expected, $parser->parse($text, $config));
	}

}
