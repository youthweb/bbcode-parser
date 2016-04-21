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

/**
 * Implements a [q] code definition that provides the following syntax:
 *
 * [q=Author]
 * Quotetext
 * [/q]
 */

class QOption extends Q
{

	public function __construct()
	{
		parent::__construct();

		$this->useOption = true;
	}

}
