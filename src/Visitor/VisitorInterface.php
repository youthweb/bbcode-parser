<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * (c) Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\Visitor;

use JBBCode\NodeVisitor;
use JBBCode\DocumentElement;
use JBBCode\TextNode;
use JBBCode\ElementNode;
use Youthweb\BBCodeParser\Config;

/**
 * Visitor interface
 */

interface VisitorInterface extends NodeVisitor
{

	/**
	 * Set the config
	 *
	 * @param  Config $config The config object
	 * @return self
	 */
	public function setConfig(Config $config);

}
