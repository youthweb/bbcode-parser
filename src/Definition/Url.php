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
use Youthweb\BBCodeParser\Validation;

/**
 * Implements a [url] code definition that provides the following syntax:
 *
 * [url]example.com[/url]
 */

class Url extends CodeDefinition
{
    public $config;

    public function __construct(Config $config)
    {
        $this->parseContent = true;
        $this->useOption = false;
        $this->setTagName('url');
        $this->nestLimit = -1;

        $this->config = $config;
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

        $param = trim($param);

        if ($content == '' and $param == '') {
            return '';
        }

        $short_url = $this->config->get('callbacks.url_content.short_url');

        // Url finden
        if ($param == '') {
            // Die Url steht im Content
            $url = $content;

            // In der anzuzeigenden URL kein &amp; anzeigen
            $content = str_replace('&amp;', '&', $content);
        } else {
            // Die Url steht im Parameter
            $url = $param;
            $short_url = false;

            $content = '';

            foreach ($el->getChildren() as $child) {
                if ($child->isTextNode()) {
                    $content .= Html::escapeSpecialChars($child->getAsHTML());
                } else {
                    $content .= $child->getAsHTML();
                }
            }
        }

        // http:// voranstellen, wenn nichts angegeben
        if (! preg_match('~^[a-z]+://~i', $url)) {
            $url = 'http://' . $url;
        }

        // Wenn die URL nicht gültig ist, zeigen wir nur den Text
        if (! $this->config->getValidation()->isValidUrl($url)) {
            return $content;
        }

        return Html::anchorFromConfig($url, $content, $this->config);
    }
}
