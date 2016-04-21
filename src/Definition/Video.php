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
 * Implements a [video] code definition that provides the following syntax:
 *
 * [video]http://www.youtube.com/watch?v=x_O0YiUFtxc[/video]
 */

class Video extends V
{

	public function __construct(Config $config)
	{
		parent::__construct($config);

		$this->setTagName('video');
	}

}
