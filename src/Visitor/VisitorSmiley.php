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

namespace Youthweb\BBCodeParser\Visitor;

use JBBCode\DocumentElement;
use JBBCode\TextNode;
use JBBCode\ElementNode;
use Youthweb\BBCodeParser\Config;

/**
 * Smiley Visitor
 *
 * Dieser Visitor dient als Beispiel für einen Visitor
 */

class VisitorSmiley implements VisitorInterface
{
    /**
     * @var Youthweb\BBCodeParser\Config
     */
    private $config;

    /**
     * Set the config
     *
     * @param Config $config The config object
     *
     * @return self
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    public function visitDocumentElement(DocumentElement $document_element)
    {
        foreach ($document_element->getChildren() as $child) {
            $child->accept($this);
        }
    }

    public function visitTextNode(TextNode $text_node)
    {
        $smiley_rules = $this->getSmileyRules();

        $text_node->setValue(str_replace($smiley_rules[0], $smiley_rules[1], $text_node->getValue()));
    }

    public function visitElementNode(ElementNode $element_node)
    {
        if ($element_node->getCodeDefinition()->parseContent()) {
            foreach ($element_node->getChildren() as $child) {
                $child->accept($this);
            }
        }
    }

    /**
     * Holt die Regeln für die Smilies
     *
     * @return array
     */
    private function getSmileyRules()
    {
        // Alle Smilies sind hier aufgelistet
        // @see https://github.com/youthweb/smiley-emoji-migration
        $smilies = [
            ':-)' => 'smile0001.gif',
            ';-)' => 'winking.gif',
            ':super:' => '489.gif',
            ':-[' => '49_2.gif',
        ];

        $codes = [];
        $html = [];

        foreach ($smilies as $code => $filename) {
            $url = 'https://youthweb.net/vendor/smilies/' . $filename;

            $codes[] = $code;
            $html[] = '<img src="' . $url . '" alt="' . $code . '" title="' . $code . '" />';
        }

        $rules = [$codes, $html];

        return $rules;
    }
}
