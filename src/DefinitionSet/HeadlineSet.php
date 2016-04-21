<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * (c) Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\DefinitionSet;

use JBBCode\CodeDefinitionSet;
use JBBCode\CodeDefinitionBuilder;
use Youthweb\BBCodeParser\Config;

/**
 * Headlinesatz an BBCode Definitionen
 */

class HeadlineSet implements CodeDefinitionSet
{

	/** @var array Die Definitionen */
	protected $definitions = array();

	/**
	 * Definiert die Definitionen
	 *
	 * @return void
	 */
	public function __construct(Config $config)
	{
		/* [h1] headline 1 tag */
		$builder = new CodeDefinitionBuilder('h1', '<!-- no_p --><h1>{param}</h1><!-- no_p -->');
		array_push($this->definitions, $builder->build());

		/* [h2] headline 2 tag */
		$builder = new CodeDefinitionBuilder('h2', '<!-- no_p --><h2>{param}</h2><!-- no_p -->');
		array_push($this->definitions, $builder->build());

		/* [h3] headline 3 tag */
		$builder = new CodeDefinitionBuilder('h3', '<!-- no_p --><h3>{param}</h3><!-- no_p -->');
		array_push($this->definitions, $builder->build());

		/* [h4] headline 4 tag */
		$builder = new CodeDefinitionBuilder('h4', '<!-- no_p --><h4>{param}</h4><!-- no_p -->');
		array_push($this->definitions, $builder->build());

		/* [h5] headline 5 tag */
		$builder = new CodeDefinitionBuilder('h5', '<!-- no_p --><h5>{param}</h5><!-- no_p -->');
		array_push($this->definitions, $builder->build());

		/* [h6] headline 6 tag */
		$builder = new CodeDefinitionBuilder('h6', '<!-- no_p --><h6>{param}</h6><!-- no_p -->');
		array_push($this->definitions, $builder->build());
	}

	/**
	 * Gibt die Definitionen zurück
	 *
	 * @return array Die Definitionen
	 */
	public function getCodeDefinitions()
	{
		return $this->definitions;
	}

}
