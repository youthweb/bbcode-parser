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
