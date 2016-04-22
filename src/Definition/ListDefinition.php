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
use Youthweb\BBCodeParser\Html;

/**
 * Implements a [list] code definition that provides the following syntax:
 *
 * [list]
 * [*] first item
 * [*] second item
 * [*] third item
 * [/list]
 */

class ListDefinition extends CodeDefinition
{

	public function __construct()
	{
		$this->parseContent = true;
		$this->useOption = false;
		$this->setTagName('list');
		$this->nestLimit = -1;
	}

	public function asHtml(ElementNode $el)
	{
		$content = '';

		foreach ( $el->getChildren() as $child )
		{
			$content .= $child->getAsHTML();
		}

		$content = trim($content);

		if ( $content == '' )
		{
			return '';
		}

		$param = $el->getAttribute();

		if ( is_array($param) )
		{
			$param = array_shift($param);
		}

		$list_type = false;
		$list_attr = array();

		if ( in_array($param, array('a', 'A', 'i', 'I')) )
		{
			$list_type = 'ol';
			$list_attr['type'] = $param;
		}

		if ( ! $list_type )
		{
			//Wir prüfen, ob eine Zahl angegeben wurde
			if(preg_match('/^(-){0,1}[0-9]+$/', $param))
			{
				$list_type = 'ol';
				$list_attr['start'] = intval($param);
			}
		}

		//Default ist ul, $param ist dann egal
		if ( ! $list_type )
		{
			$list_type = 'ul';
			$list_attr['type'] = 'disc';
		}

		$delimiter = '[*]';
		if ( substr($content, 0, strlen($delimiter)) === $delimiter )
		{
			$content = substr_replace( $content, '', 0, strlen($delimiter) );
		}

		//Text in Lines aufteilen; jede Line ist ein neuer Listenpunkt
		$lines = explode($delimiter, $content);

		$items = array();

		foreach ( $lines as $line )
		{
			$items[] = trim($line);
		}

		return '<!-- no_p -->' . Html::$list_type($items, $list_attr) . '<!-- no_p -->';
	}

}
