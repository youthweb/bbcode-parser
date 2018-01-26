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
 * Implements a [email] code definition that provides the following syntax:
 *
 * [email]mail@example.com[/email]
 */

class Email extends CodeDefinition
{
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
                $content .= $child->getAsHTML();
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
