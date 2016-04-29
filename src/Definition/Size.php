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
 * Implements a [size] code definition that provides the following syntax:
 *
 * [size=75]small text[/size]
 */

class Size extends CodeDefinition
{

	public function __construct(Config $config)
	{
		$this->parseContent = true;
		$this->useOption = false;
		$this->setTagName('size');
		$this->nestLimit = -1;

		$this->config = $config;
	}

	public function asHtml(ElementNode $el)
	{
		$content = '';

		foreach ( $el->getChildren() as $child )
		{
			$content .= $child->getAsHTML();
		}

		$param = $el->getAttribute();

		if ( is_array($param) )
		{
			$param = array_shift($param);
		}

		$param = trim($param);

		if ( $content == '' )
		{
			return '';
		}

		$size = intval($param);

		if ( $size == 0 )
		{
			$size = 100;
		}

		// Mindestgröße: 75%
		$size = max($size, $this->config->get('callbacks.size_param.min_size'));

		// Maximale Größe: 150%
		$size = min($size, $this->config->get('callbacks.size_param.max_size'));

		return Html::span($content, ['style' => 'font-size:' . $size . '%;']);
	}

}
