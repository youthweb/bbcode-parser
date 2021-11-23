<?php
/*
 * A BBCode-to-HTML parser for youthweb.net
 * Copyright (C) 2016-2021  Youthweb e.V. <info@youthweb.net>

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
    public $config;

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
        try {
            $filter = $this->config->get('filter.video', null);

            if (is_object($filter) and $filter instanceof FilterInterface) {
                $filter->setConfig($this->config);

                return $filter->execute($el);
            }
        } catch (FilterException $e) {
        }

        // Video nur verlinken
        $definition = new UrlOption($this->config);

        return $definition->asHtml($el);
    }
}
