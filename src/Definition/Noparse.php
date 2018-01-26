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
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Html;

/**
 * Implements a [noparse] definition that provides the following syntax:
 *
 * [noparse][b]some bbcode[/b][/noparse]
 */

class Noparse extends CodeDefinition
{
    public function __construct()
    {
        $this->parseContent = false;
        $this->useOption = false;
        $this->setTagName('noparse');
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

        return htmlspecialchars($content, $flags, $encoding, $double_encode);
    }
}
