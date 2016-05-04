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

/**
 * Diese Klasse verwaltet die Config
 */

class Config
{

	/**
	 * @var array The config data
	 */
	protected $data = [];

	/**
	 * @var ValidationInterface The validation class
	 */
	protected $validation;


	/**
	 * @var array default config
	 */
	protected $default_config = array(
		/**
		 * Psr\Cache\CacheItemPoolInterface
		 */
		'cacheitempool' => null,
		/**
		 * Sollen die Standard BBCodes ersetzt werden?
		 */
		'parse_default' => true,
		/**
		 * Sollen Smilies ersetzt werden?
		 */
		'parse_smilies' => true,
		/**
		 * Sollen BBCodes für h1 bis h6 unterstützt werden?
		 */
		'parse_headlines' => false,
		/**
		 * Sollen Videos eingebunden werden?
		 */
		'embed_videos' => true,
		/**
		 * Sollen Bilder eingebunden werden?
		 */
		'embed_images' => true,
		/**
		 * Max. Höhe von eingebetteten Elementen (in px)
		 */
		'embed_maxheight' => 350,
		/**
		 * Max. Breite von eingebetteten Elementen (in px)
		 */
		'embed_maxwidth' => 425,
		/**
		 * Filter für Image und Video Einbettung (FilterInterface oder null)
		 */
		'filter' => array(
			'image' => null,
			'video' => null,
		),
		/**
		 * Visitor für Smilies (JBBCode\NodeVisitor oder null)
		 */
		'visitor' => array(
			'smiley' => null,
		),
		/**
		 * Callback Config
		 */
		'callbacks' => array(
			'email_content' => array(
				'protect_email' => false,
			),
			'url_content' => array(
				'short_url' => false, // Lange URLs kürzen?
				'short_url_length' => 55, // Maximale Anzeigelänge für URLs (mind. 20 möglich)
				'target' => '_blank', // null, wenn kein target gesetzt werden soll
			),
			'color_param' => array(
				// Erlaubte Farbnamen; siehe auch: (@link http://www.w3schools.com/cssref/css_colornames.asp)
				'allowed_colors' => array('Red', 'Green', 'Blue', 'Yellow', 'Black', 'White', 'Orange', 'Grey'),
				// Standard-Farbe
				'default_color' => 'Red',
			),
			'size_param' => array(
				'max_size' => 150,
				'min_size' => 75,
			),
			'image' => array(
				'force_check' => true,
			),
		),
	);

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->data = $this->default_config;
	}

	/**
	 * Set a config value
	 *
	 * @param  string $key   The key
	 * @param  mixed  $value The value
	 * @param  self
	 */
	public function set($key, $value)
	{
		$keys = explode('.', $key);

		$array = &$this->data;

		while ( count($keys) > 1 )
		{
			$_key = array_shift($keys);

			if ( ctype_digit($_key) )
			{
				// Make the key an integer
				$_key = (int) $_key;
			}

			if ( ! isset($array[$_key]) )
			{
				$array[$_key] = array();
			}

			$array = &$array[$_key];
		}

		// Set key on inner-most array
		$array[array_shift($keys)] = $value;

		return $this;
	}

	/**
	 * Get a config value
	 *
	 * @param  string $key     The key
	 * @param  mixed  $default The default value
	 * @param  self
	 */
	public function get($key, $default = null)
	{
		$keys = explode('.', $key);

		$array = $this->data;

		do
		{
			$_key = array_shift($keys);

			if ( ctype_digit($_key) )
			{
				// Make the key an integer
				$_key = (int) $_key;
			}

			if ( ! isset($array[$_key]) )
			{
				// Unable to dig deeper
				break;
			}

			if ($keys)
			{
				if ( ! is_array($array[$_key]) )
				{
					// Unable to dig deeper
					break;
				}

				// Dig down into the next part of the path
				$array = $array[$_key];
			}
			else
			{
				// Found the requested value
				return $array[$_key];
			}
		}
		while ($keys);

		return $default;
	}

	/**
	 * Returns the validation class
	 *
	 * @return ValidationInterface The validation class
	 */
	public function getValidation()
	{
		if ( is_null($this->validation) )
		{
			$this->validation = new Validation($this);
		}

		return $this->validation;
	}

	/**
	 * Merge a config array
	 *
	 * Undocumented function long description
	 *
	 * @param array $config The config
	 * @return self
	 */
	public function mergeRecursive(array $config)
	{
		$this->data = array_replace_recursive($this->data, $config);

		return $this;
	}

}
