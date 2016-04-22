<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * (c) Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser;

use JBBCode\Parser;
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
	 * parst einen Text mit BBCode-Regeln
	 *
	 * Zur Anwendung kann HtmlParser::parse() verwendet werden
	 *
	 * @param  string  $text  Der Text, der geparst werden soll
	 * @param  array  $config  config array
	 */
	public function parse($text, array $config = array())
	{
		$this->config = new Config();

		$this->config->mergeRecursive($config);

		$parser = new Parser();

		$definition_sets = $this->getDefinitionSets();

		// Wenn keine Definitions definiert wurde, brechen wir ab
		if ( count($definition_sets) === 0 )
		{
			return $text;
		}

		// Die DefinitionSets dem Parser zuweisen
		foreach ( $definition_sets as $definition_set )
		{
			$parser->addCodeDefinitionSet($definition_set);
		}

		$parser->parse($text);

		// Sollen Smilies geparset werden?
		if ( $this->config->get('parse_smilies')  )
		{
			$visitor = $this->config->get('visitor.smiley');

			if ( is_object($visitor) and $visitor instanceof \JBBCode\NodeVisitor )
			{
				$parser->accept($visitor);
			}
		}

		$text = $parser->getAsHtml();

		$text = $this->addParagraphs($text);

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
		$sets = array();

		// BBCode-Regeln holen
		if( $this->config->get('parse_default') )
		{
			array_push( $sets, new DefaultSet($this->config) );
		}

		// Sollen Headlines [h1]-[h6] geparset werden? (Nur für News, Hilfe, etc)
		if( $this->config->get('parse_headlines') )
		{
			array_push( $sets, new HeadlineSet($this->config) );
		}

		return $sets;
	}

	/**
	 * Setzt p-Absätze im Text ein
	 *
	 * @param string $text Der Text
	 * @return string Der umgewandelte Text
	 */
	protected function addParagraphs($text)
	{
		// Verarbeitet \r\n's zuerst, so dass sie nicht doppelt konvertiert werden
		$text = str_replace(array("\r\n", "\r"), "\n", $text);

		$parts = explode('<!-- no_p -->', $text);
		$in_p = false;
		$new_text = "";

		foreach ( $parts as $part )
		{
			$in_p = ($in_p === false) ? true : false;

			if ( trim($part) == "" )
			{
				continue;
			}

			if ( ! $in_p )
			{
				$new_text .= $part . "\n";
				continue;
			}

			// Doppelte Umbrüche gegen Platzhalter ersetzen
			$part = str_replace("\n\n", "<!-- p_end -->", trim($part));
			// Zeilenumbrüche einfügen
			$part = nl2br($part, true);
			// Platzhalter für doppelte Umbrüche gegen Absatz-Wechsel ersetzen
			$part = str_replace("<!-- p_end -->", "</p>\n<p>", trim($part));

			// Leere Absätze entfernen
			$part = str_replace(array("<p></p>\r\n", "<p></p>\n", "<p></p>"), "", $part);

			$new_text .= "<p>" . trim($part) . "</p>\n";
		}

		return trim($new_text);
	}

	/**
	 * Fügt Erklärungen in einen Text ein
	 *
	 * @param string $text Der Text
	 * @return string Der Text mit den Erläuterungen
	 */
	protected function addExplanations($text)
	{
		$text = str_ireplace(' yw ', ' <acronym title="Youthweb">YW</acronym> ', $text);

		return $text;
	}

}
