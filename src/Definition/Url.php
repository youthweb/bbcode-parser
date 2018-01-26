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
use Youthweb\BBCodeParser\Validation;

/**
 * Implements a [url] code definition that provides the following syntax:
 *
 * [url]example.com[/url]
 */

class Url extends CodeDefinition
{
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
            $content .= $child->getAsText();
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
                $content .= $child->getAsHTML();
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
