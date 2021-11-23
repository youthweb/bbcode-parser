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
 * Implements a [email] code definition that provides the following syntax:
 *
 * [email]mail@example.com[/email]
 */

class Email extends CodeDefinition
{
    public $config;

    public function __construct(Config $config)
    {
        $this->parseContent = true;
        $this->useOption = false;
        $this->setTagName('email');
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

        if ($param == '') {
            // Die Email steht im Content
            $email = '';

            foreach ($el->getChildren() as $child) {
                $email .= $child->getAsText();
            }
        } else {
            // Die Email steht im Parameter
            $email = $param;

            $content = '';

            foreach ($el->getChildren() as $child) {
                if ($child->isTextNode()) {
                    $content .= Html::escapeSpecialChars($child->getAsHTML());
                } else {
                    $content .= $child->getAsHTML();
                }
            }
        }

        // Nur Content anzeigen, wenn keine gültige Email angegeben wurde
        if (! $this->config->getValidation()->isValidEmail($email)) {
            return $content;
        }

        // Mail vor Bots schützen?
        if ($this->config->get('callbacks.email_content.protect_email')) {
            return Html::mail_to_safe($email, $content);
        }

        return Html::mail_to($email, $content);
    }
}
