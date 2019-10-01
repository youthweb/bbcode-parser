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

/**
 * Implements a [q] code definition that provides the following syntax:
 *
 * [q]
 * Quotetext
 * [/q]
 */

class Q extends CodeDefinition
{
    public function __construct()
    {
        $this->parseContent = true;
        $this->useOption = false;
        $this->setTagName('q');
        $this->nestLimit = -1;
    }

    public function asHtml(ElementNode $el)
    {
        $content = '';

        foreach ($el->getChildren() as $child) {
            $content .= $child->getAsHTML();
        }

        $param = $el->getAttribute();

        if (is_array($param)) {
            $param = array_shift($param);
        }

        $param = (string) trim($param);

        if (preg_match('/^\<blockquote/', trim($content)) == 0) {
            $content = '<cite>' . $param . '</cite>' . $content;
        } else {
            $search = '</blockquote>';
            $replace = '</blockquote><cite>' . $param . '</cite>';

            $len = strlen($search);
            $pos = strrpos($content, $search);

            $content = substr_replace($content, $replace, $pos, $len);
        }

        return '<blockquote title="Zitat">' . $content . '</blockquote>';
    }
}
