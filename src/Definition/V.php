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
use Youthweb\BBCodeParser\Filter\FilterInterface;
use Youthweb\BBCodeParser\Filter\FilterException;

/**
 * Implements a [v] code definition that provides the following syntax:
 *
 * [v]http://www.youtube.com/watch?v=x_O0YiUFtxc[/v]
 * [v]http://vimeo.com/11902475[/v]
 * [v]http://www.dailymotion.com/video/x10hfkd_1-minute-countdown-clock-timer_videogames[/v]
 * [v]https://youtu.be/x_O0YiUFtxc[/v]
 */

class V extends CodeDefinition
{

	public function __construct(Config $config)
	{
		$this->parseContent = false;
		$this->useOption = false;
		$this->setTagName('v');
		$this->nestLimit = -1;

		$this->config = $config;
	}

	public function asHtml(ElementNode $el)
	{
		// Try to execute the video filter
		try
		{
			$filter = $this->config->get('filter.video', null);

			if ( is_object($filter) and $filter instanceof FilterInterface )
			{
				$filter->setConfig($this->config);

				return $filter->execute($el);
			}
		}
		catch (FilterException $e) {}

		// Video nur verlinken
		$definition = new UrlOption($this->config);

		return $definition->asHtml($el);
	}

}
