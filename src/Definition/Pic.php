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
 * Implements a [PIC] code definition that provides the following syntax:
 *
 * [PIC]example.com/image.jpg[/PIC]
 * [PIC]321654[/PIC]
 */

class Pic extends Image
{

	public function __construct(Config $config)
	{
		parent::__construct($config);

		$this->setTagName('PIC');
	}

}
