<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
