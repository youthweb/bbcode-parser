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
 * Implements a [quote] code definition that provides the following syntax:
 *
 * [quote=Author]
 * Quotetext
 * [/qoute]
 */

class QuoteOption extends Quote
{

	public function __construct()
	{
		parent::__construct();

		$this->useOption = true;
	}

}
