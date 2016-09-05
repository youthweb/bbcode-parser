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
use Youthweb\BBCodeParser\Definition;

/**
 * Standardsatz an BBCode Definitionen
 */

class DefaultSet implements CodeDefinitionSet
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
		/* [b] bold tag */
		$builder = new CodeDefinitionBuilder('b', '<b>{param}</b>');
		array_push($this->definitions, $builder->build());
		// Depreacated [F] tag
		$builder->setTagName('F');
		array_push($this->definitions, $builder->build());

		/* [i] italics tag */
		$builder = new CodeDefinitionBuilder('i', '<i>{param}</i>');
		array_push($this->definitions, $builder->build());
		// Depreacated [K] tag
		$builder->setTagName('K');
		array_push($this->definitions, $builder->build());

		/* [u] underline tag */
		$builder = new CodeDefinitionBuilder('u', '<u>{param}</u>');
		array_push($this->definitions, $builder->build());

		/* [code] code tag */
		array_push($this->definitions, new Definition\Code());

		/* [noparse] noparse tag */
		array_push($this->definitions, new Definition\Noparse());

		/* [list] list tag */
		array_push($this->definitions, new Definition\ListDefinition());
		array_push($this->definitions, new Definition\ListOption());

		/* [q] quote tag */
		array_push($this->definitions, new Definition\Q());
		array_push($this->definitions, new Definition\QOption());
		array_push($this->definitions, new Definition\Quote());
		array_push($this->definitions, new Definition\QuoteOption());
		// Deprecated Z tag
		array_push($this->definitions, new Definition\Z());
		array_push($this->definitions, new Definition\ZOption());

		/* [email] email tag */
		array_push($this->definitions, new Definition\Email($config));
		array_push($this->definitions, new Definition\EmailOption($config));

		/* [url] url tag */
		array_push($this->definitions, new Definition\Url($config));
		array_push($this->definitions, new Definition\UrlOption($config));
		array_push($this->definitions, new Definition\YwlinkOption($config));

		/* [img] image tag */
		array_push($this->definitions, new Definition\Image($config));
		array_push($this->definitions, new Definition\Pic($config));

		/* [v] video tag */
		array_push($this->definitions, new Definition\V($config));
		array_push($this->definitions, new Definition\Video($config));
		array_push($this->definitions, new Definition\Youtube($config));

		/* [color] color tag */
		array_push($this->definitions, new Definition\Color($config));
		array_push($this->definitions, new Definition\ColorOption($config));

		/* [size] size tag */
		array_push($this->definitions, new Definition\Size($config));
		array_push($this->definitions, new Definition\SizeOption($config));
	}

	/**
	* Returns an array of the default code definitions.
	*/
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
