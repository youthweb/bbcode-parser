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
use Art4\UrlLinker\UrlLinker;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Html;
use Youthweb\BBCodeParser\HtmlTextNode;

/**
 * Url Visitor
 *
 * Dieser Visitor macht Links anklickbar
 *
 * @deprecated 1.10.0, the {@see \Youthweb\BBCodeParser\Engine} links bare urls itself
 */
class VisitorUrl implements VisitorInterface
{
    private ?\Youthweb\BBCodeParser\Config $config = null;

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

        return $this;
    }

    public function visitDocumentElement(DocumentElement $document_element)
    {
        foreach ($document_element->getChildren() as $child) {
            $child->accept($this);
        }
    }

    public function visitTextNode(TextNode $text_node)
    {
        $linked = false;

        $urllinker = new UrlLinker([
            'emailLinkCreator' => fn($email, $content)
                // Email-Adressen sollen nicht verlinkt werden
                => $email,
            'htmlLinkCreator' => function ($url, $content) use (&$linked) {
                $linked = true;

                // Wir vergeben extra zweimal $url, weil $content durch UrlLinker gekürzt wird
                return Html::anchorFromConfig($url, $url, $this->config);
            },
        ]);

        $text_node->setValue($urllinker->linkUrlsAndEscapeHtml($text_node->getValue()));

        if ($linked) {
            $this->markAsHtml($text_node);
        }
    }

    public function visitElementNode(ElementNode $element_node)
    {
        // Nur nach Urls suchen, wenn nicht in URL-Tag und der Content geparst werden soll
        if ($element_node->getCodeDefinition()->getTagName() !== 'url' && $element_node->getCodeDefinition()->parseContent()) {
            foreach ($element_node->getChildren() as $child) {
                $child->accept($this);
            }
        }
    }

    /**
     * Replaces the text node by a node with finished HTML, so a formatting tag does not escape the link again
     */
    private function markAsHtml(TextNode $text_node): void
    {
        $parent = $text_node->getParent();

        // The text of the document itself is not escaped again
        if (! $parent instanceof ElementNode || $parent instanceof DocumentElement) {
            return;
        }

        // A node cannot be replaced in place: the children are removed and added again in their order.
        // The old node is dropped, the parent of every added node is set by addChild().
        $children = $parent->getChildren();

        foreach ($children as $child) {
            $parent->removeChild($child);
        }

        foreach ($children as $child) {
            $parent->addChild($child === $text_node ? new HtmlTextNode($text_node->getValue()) : $child);
        }
    }
}
