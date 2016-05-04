<?php

namespace Youthweb\BBCodeParser\Tests\Fixtures;

use Youthweb\BBCodeParser\Validation;

/**
 * Validation Mock
 */
class ValidationMock extends Validation
{
	/**
	 * reset $_valid_img_url_counter
	 */
	public static function resetImageCounter()
	{
		static::$_valid_img_url_counter = 0;
	}
}
