<?php
/*
 * A BBCode-to-HTML parser for youthweb.net
 * Copyright (C) 2016-2019  Youthweb e.V. <info@youthweb.net>

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
use Youthweb\BBCodeParser\Html;

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

        foreach ($el->getChildren() as $child) {
            if ($child->isTextNode()) {
                $content .= Html::escapeSpecialChars($child->getAsHTML());
            } else {
                $content .= $child->getAsHTML();
            }
        }

        $param = $el->getAttribute();

        if (is_array($param)) {
            $param = array_shift($param);
        }

        $param = trim($param);

        if ($content == '' and $param == '') {
            return '';
        }

        // Erlaubte Farbnamen
        $allowed_colors = (array) $this->config->get('callbacks.color_param.allowed_colors');

        $color = mb_convert_case($param, MB_CASE_TITLE);

        // Wenn im Parameter keine erlaubte Farbe steht
        if (! in_array($color, $allowed_colors)) {
            // Prüfen, ob eine hexadezimale Farbe angegeben wurde
            if (strlen($param) == 7 && $param[0] == '#' and preg_match('~#[a-f0-9]{6}~i', $param)) {
                $color = $param;
            } else {
                $color = $this->config->get('callbacks.color_param.default_color');
            }
        }

        // Ansonsten ist der Farbnamen gülig
        return Html::span($content, ['style' => 'color:' . $color . ';']);
    }
}
