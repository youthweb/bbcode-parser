<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
