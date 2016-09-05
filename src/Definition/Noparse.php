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

use JBBCode\CodeDefinition;
use JBBCode\ElementNode;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Html;

/**
 * Implements a [noparse] definition that provides the following syntax:
 *
 * [noparse][b]some bbcode[/b][/noparse]
 */

class Noparse extends CodeDefinition
{

	public function __construct()
	{
		$this->parseContent = false;
		$this->useOption = false;
		$this->setTagName('noparse');
		$this->nestLimit = -1;
	}

	public function asHtml(ElementNode $el)
	{
		$content = '';

		foreach ( $el->getChildren() as $child )
		{
			$content .= $child->getAsHTML();
		}

		return htmlspecialchars($content);
	}

}
