<?php

namespace Youthweb\BBCodeParser\Tests\Unit;

use Youthweb\BBCodeParser\Validation;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function testValidImageUrl()
	{
		$validation = new Validation();

		$this->assertSame(false, $validation->isValidImageUrl('foobar', false));
	}
}
