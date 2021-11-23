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

namespace Youthweb\BBCodeParser\Visitor;

use JBBCode\DocumentElement;
use JBBCode\TextNode;
use JBBCode\ElementNode;
use Youthweb\UrlLinker\UrlLinker;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Html;

/**
 * Url Visitor
 *
 * Dieser Visitor macht Links anklickbar
 */

class VisitorUrl implements VisitorInterface
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
        $urllinker = new UrlLinker([
            'emailLinkCreator' => function ($email, $content) {
                // Email-Adressen sollen nicht verlinkt werden
                return $email;
            },
            'htmlLinkCreator' => function ($url, $content) {
                // Wir vergeben extra zweimal $url, weil $content durch UrlLinker gekürzt wird
                return Html::anchorFromConfig($url, $url, $this->config);
            },
        ]);

        $text_node->setValue($urllinker->linkUrlsAndEscapeHtml($text_node->getValue()));
    }

    public function visitElementNode(ElementNode $element_node)
    {
        // Nur nach Urls suchen, wenn nicht in URL-Tag und der Content geparst werden soll
        if ($element_node->getCodeDefinition()->getTagName() !== 'url' and $element_node->getCodeDefinition()->parseContent()) {
            foreach ($element_node->getChildren() as $child) {
                $child->accept($this);
            }
        }
    }
}
