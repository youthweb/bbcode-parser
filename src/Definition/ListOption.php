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
 * Implements a [list=X] code definition that provides the following syntax:
 *
 * [list=1]
 * [*] first item
 * [*] second item
 * [*] third item
 * [/list]
 */

class ListOption extends ListDefinition
{

	public function __construct()
	{
		parent::__construct();

		$this->useOption = true;
	}

}
