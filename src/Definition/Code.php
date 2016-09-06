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
 * Implements a [code] definition that provides the following syntax:
 *
 * [code]<span>some html</span>[/code]
 */

class Code extends CodeDefinition
{

	public function __construct()
	{
		$this->parseContent = false;
		$this->useOption = false;
		$this->setTagName('code');
		$this->nestLimit = -1;
	}

	public function asHtml(ElementNode $el)
	{
		$content = '';

		foreach ( $el->getChildren() as $child )
		{
			$content .= $child->getAsHTML();
		}

		$flags = ENT_COMPAT | ENT_HTML401;
		$encoding = ini_get("default_charset");
		$double_encode = false; // Do not double encode

		return '<!-- no_p --><pre><code>' . htmlspecialchars($content, $flags, $encoding, $double_encode) . '</code></pre><!-- no_p -->';
	}

}
