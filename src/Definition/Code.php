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

        foreach ($el->getChildren() as $child) {
            $content .= $child->getAsHTML();
        }

        $flags = ENT_COMPAT | ENT_HTML401;
        $encoding = ini_get('default_charset');
        $double_encode = false; // Do not double encode

        return '<!-- no_p --><pre><code>' . htmlspecialchars($content, $flags, $encoding, $double_encode) . '</code></pre><!-- no_p -->';
    }
}
