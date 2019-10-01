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

namespace Youthweb\BBCodeParser;

use JBBCode\Parser;
use Youthweb\BBCodeParser\Visitor\VisitorCollection;
use Youthweb\BBCodeParser\Visitor\VisitorCollectionInterface;
use Youthweb\BBCodeParser\Visitor\VisitorInterface;
use Youthweb\BBCodeParser\DefinitionSet\DefaultSet;
use Youthweb\BBCodeParser\DefinitionSet\HeadlineSet;

/**
 * Diese Klasse verwaltet das Parsen von BBCodes mithilfe der JBBCode Library
 */

class Manager
{
    /**
     * @var Config The config object
     */
    protected $config = null;

    /**
     * @var Youthweb\BBCodeParser\Visitor\VisitorCollection
     */
    private $visitorCollection;

    /**
     * Create the Manager
     *
     * @param Youthweb\BBCodeParser\Visitor\VisitorCollectionInterface|null $visitorCollection
     */
    public function __construct(VisitorCollectionInterface $visitorCollection = null)
    {
        if ($visitorCollection === null) {
            $visitorCollection = new VisitorCollection();
        }

        $this->visitorCollection = $visitorCollection;
    }

    /**
     * parst einen Text mit BBCode-Regeln
     *
     * @param string $text   Der Text, der geparst werden soll
     * @param array  $config config array
     */
    public function parse($text, array $config = [])
    {
        $this->config = new Config();

        $this->config->mergeRecursive($config);

        $parser = new Parser();

        $definition_sets = $this->getDefinitionSets();
        $visitors = $this->visitorCollection->getVisitors();

        // Wenn keine Definitions oder Visitors definiert wurden, brechen wir ab
        if (count($definition_sets) === 0 and count($visitors) === 0) {
            return $text;
        }

        // Die DefinitionSets dem Parser zuweisen
        foreach ($definition_sets as $definition_set) {
            $parser->addCodeDefinitionSet($definition_set);
        }

        $parser->parse($text);

        // Sollen Urls erkannt werden?
        if ($this->config->get('parse_urls')) {
            $visitor = $this->config->get('visitor.url');

            if (is_object($visitor) and $visitor instanceof VisitorInterface) {
                $visitor->setConfig($this->config);

                $parser->accept($visitor);
            }
        }

        // Gibt es Visitors?
        foreach ($visitors as $visitor) {
            $parser->accept($visitor);
        }

        // Sollen Smilies geparset werden?
        if ($this->config->get('parse_smilies')) {
            $visitor = $this->config->get('visitor.smiley');

            if (is_object($visitor) and $visitor instanceof \JBBCode\NodeVisitor) {
                // BC: VisitorInterface wurde erst mit v1.1 eingeführt
                if ($visitor instanceof VisitorInterface) {
                    $visitor->setConfig($this->config);
                }

                $parser->accept($visitor);
            }
        }

        $text = $parser->getAsHtml();

        if (count($definition_sets) !== 0) {
            $text = $this->addParagraphs($text);
        }

        //Erklärungen hinzufügen
        $text = $this->addExplanations($text);

        return $text;
    }

    /**
     * Holt die DefinitionSets
     *
     * @return array Die Sets als \JBBCode\CodeDefinitionSet Objekte
     */
    protected function getDefinitionSets()
    {
        $sets = [];

        // BBCode-Regeln holen
        if ($this->config->get('parse_default')) {
            array_push($sets, new DefaultSet($this->config));
        }

        // Sollen Headlines [h1]-[h6] geparset werden? (Nur für News, Hilfe, etc)
        if ($this->config->get('parse_headlines')) {
            array_push($sets, new HeadlineSet($this->config));
        }

        return $sets;
    }

    /**
     * Setzt p-Absätze im Text ein
     *
     * @param string $text Der Text
     *
     * @return string Der umgewandelte Text
     */
    protected function addParagraphs($text)
    {
        // Verarbeitet \r\n's zuerst, so dass sie nicht doppelt konvertiert werden
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        $parts = explode('<!-- no_p -->', $text);
        $in_p = false;
        $new_text = '';

        foreach ($parts as $part) {
            $in_p = ($in_p === false) ? true : false;

            if (trim($part) == '') {
                continue;
            }

            if (! $in_p) {
                $new_text .= $part . "\n";

                continue;
            }

            // Doppelte Umbrüche gegen Platzhalter ersetzen
            $part = str_replace("\n\n", '<!-- p_end -->', trim($part));
            // Zeilenumbrüche einfügen
            $part = nl2br($part, true);
            // Platzhalter für doppelte Umbrüche gegen Absatz-Wechsel ersetzen
            $part = str_replace('<!-- p_end -->', "</p>\n<p>", trim($part));

            // Leere Absätze entfernen
            $part = str_replace(["<p></p>\r\n", "<p></p>\n", '<p></p>'], '', $part);

            $new_text .= '<p>' . trim($part) . "</p>\n";
        }

        return trim($new_text);
    }

    /**
     * Fügt Erklärungen in einen Text ein
     *
     * @param string $text Der Text
     *
     * @return string Der Text mit den Erläuterungen
     */
    protected function addExplanations($text)
    {
        $text = str_ireplace(' yw ', ' <acronym title="Youthweb">YW</acronym> ', $text);

        return $text;
    }
}
