<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
