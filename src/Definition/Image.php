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
use Youthweb\BBCodeParser\Validation;
use Youthweb\BBCodeParser\Filter\FilterInterface;
use Youthweb\BBCodeParser\Filter\FilterException;

/**
 * Implements a [img] code definition that provides the following syntax:
 *
 * [img]example.com/image.jpg[/img]
 * [img]321654[/img]
 */

class Image extends CodeDefinition
{

	public function __construct(Config $config)
	{
		$this->parseContent = false;
		$this->useOption = false;
		$this->setTagName('img');
		$this->nestLimit = -1;

		$this->config = $config;
	}

	public function asHtml(ElementNode $el)
	{
		// Try to execute the image filter
		try
		{
			$filter = $this->config->get('filter.image', null);

			if ( is_object($filter) and $filter instanceof FilterInterface )
			{
				$filter->setConfig($this->config);

				return $filter->execute($el);
			}
		}
		catch (FilterException $e) {}

		$content = '';

		// Soll das Bild verlinkt werden?
		$use_anchor = true;

		// Prüfen, ob wir uns innerhalb eines [url] Tags befinden
		if ( $el->closestParentOfType('url') )
		{
			$use_anchor = false;
		}

		foreach ( $el->getChildren() as $child )
		{
			$content .= $child->getAsText();
		}

		// Mögliche Leerzeichen trimmen
		$url = trim($content);

		// URL Schema voranstellen, wenn nichts angegeben wurde
		if ( $url !== '' and parse_url($url, PHP_URL_SCHEME) === null )
		{
			$url = 'http://' . $url;
		}

		// Wenn die URL nicht gültig ist, zeigen wir nur den Text
		if ( $url === '' or ! $this->config->getValidation()->isValidUrl($url) )
		{
			return $content;
		}

		// Prüfen, ob die URL wirklich auf ein Bild zeigt
		// Wenn es kein Bild ist, nicht einbetten, sondern nur verlinken
		if ( $this->config->get('callbacks.image.force_check') and ! $this->config->getValidation()->isValidImageUrl($url) )
		{
			// Bild nicht verlinken
			if ( ! $use_anchor )
			{
				return $content;
			}

			// Bild nur verlinken
			$definition = new UrlOption($this->config);

			return $definition->asHtml($el);
		}

		$attr = array(
			'class' => 'img-responsive',
			'border' => '0',
		);

		$img_code = Html::img($url, $attr);

		if ( ! $use_anchor )
		{
			return $img_code;
		}

		return Html::anchor($url, $img_code, ['target' => '_blank']);
	}

}
