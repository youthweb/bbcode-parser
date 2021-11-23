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
use Youthweb\BBCodeParser\Html;

/**
 * Implements a [b] code definition that provides the following syntax:
 *
 * [b]this text will be bold[/b]
 */

class Bold extends CodeDefinition
{
    public function __construct()
    {
        parent::__construct();
        $this->parseContent = true;
        $this->useOption = false;
        $this->setTagName('b');
        $this->setReplacementText('<b>{param}</b>');
    }

    protected function getContent(ElementNode $el)
    {
        if ($this->parseContent()) {
            $content = "";
            foreach ($el->getChildren() as $child) {
                if ($child->isTextNode()) {
                    $content .= Html::escapeSpecialChars($child->getAsHTML());
                } else {
                    $content .= $child->getAsHTML();
                }
            }
        } else {
            $content = "";
            foreach ($el->getChildren() as $child) {
                $content .= $child->getAsBBCode();
            }
        }

        return $content;
    }
}
