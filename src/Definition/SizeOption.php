<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * (c) Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\Definition;

use Youthweb\BBCodeParser\Config;

/**
 * Implements a [size] code definition that provides the following syntax:
 *
 * [size=75]small text[/size]
 */

class SizeOption extends Size
{

	public function __construct(Config $config)
	{
		parent::__construct($config);

		$this->useOption = true;
	}

}
