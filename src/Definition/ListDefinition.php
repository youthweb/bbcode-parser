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

        foreach ($el->getChildren() as $child) {
            $content .= $child->getAsHTML();
        }

        $content = trim($content);

        if ($content == '') {
            return '';
        }

        $param = $el->getAttribute();

        if (is_array($param)) {
            $param = array_shift($param);
        }

        $list_type = false;
        $list_attr = [];

        if (in_array($param, ['a', 'A', 'i', 'I'])) {
            $list_type = 'ol';
            $list_attr['type'] = $param;
        }

        if (! $list_type) {
            //Wir prüfen, ob eine Zahl angegeben wurde
            if (preg_match('/^(-){0,1}[0-9]+$/', $param)) {
                $list_type = 'ol';
                $list_attr['start'] = intval($param);
            }
        }

        //Default ist ul, $param ist dann egal
        if (! $list_type) {
            $list_type = 'ul';
            $list_attr['type'] = 'disc';
        }

        $delimiter = '[*]';
        if (substr($content, 0, strlen($delimiter)) === $delimiter) {
            $content = substr_replace($content, '', 0, strlen($delimiter));
        }

        //Text in Lines aufteilen; jede Line ist ein neuer Listenpunkt
        $lines = explode($delimiter, $content);

        $items = [];

        foreach ($lines as $line) {
            $items[] = trim($line);
        }

        return '<!-- no_p -->' . Html::$list_type($items, $list_attr) . '<!-- no_p -->';
    }
}
