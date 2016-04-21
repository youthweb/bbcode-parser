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

/**
 * Implements a [color] code definition that provides the following syntax:
 *
 * [color=green]green text comes here[/color]
 */

class Color extends CodeDefinition
{

	public function __construct(Config $config)
	{
		$this->parseContent = true;
		$this->useOption = false;
		$this->setTagName('color');
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

		if ( $content == '' and $param == '' )
		{
			return '';
		}

		// Erlaubte Farbnamen
		$allowed_colors = (array) $this->config->get('callbacks.color_param.allowed_colors');

		$color = mb_convert_case($param, MB_CASE_TITLE);

		// Wenn im Parameter keine erlaubte Farbe steht
		if ( ! in_array($color, $allowed_colors) )
		{
			// Prüfen, ob eine hexadezimale Farbe angegeben wurde
			if ( strlen($param) == 7 && $param[0] == '#' and preg_match('~#[a-f0-9]{6}~i', $param) )
			{
				$color = $param;
			}
			else
			{
				$color = $this->config->get('callbacks.color_param.default_color');
			}
		}

		// Ansonsten ist der Farbnamen gülig
		return '<span style="color:'.$color.';">'.$content.'</span>';
	}

}
