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
 * Einfache Validation-Klasse, angelehnt an Fuel\Core\Validation
 */

class Validation implements ValidationInterface
{

	/**
	 * @var int Counter for image url checks
	 */
	protected static $_valid_img_url_counter = 0;

	/**
	 * Validate email using PHP's filter_var()
	 *
	 * @param   string
	 * @return  bool
	 */
	public function isValidEmail($val)
	{
		return $this->_empty($val) || filter_var($val, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Validate URL using PHP's filter_var()
	 *
	 * @param   string
	 * @return  bool
	 */
	public function isValidUrl($val)
	{
		return $this->_empty($val) || filter_var($val, FILTER_VALIDATE_URL);
	}

	/**
	 * Validierung einer URL zu einem Bild
	 */
	public function isValidImageUrl($val, $force_check = true)
	{
		$cache_identifier = 'img_urls.' . md5($val);

		// try to retrieve the cache and save to $is_valid var
		try
		{
			// TODO: PSR-6 Cache verwenden
			$is_valid = \Cache::get($cache_identifier);
		}
		catch (\CacheNotFoundException $e)
		{
			$is_valid = false;

			// Keine Prüfung und Cachespeicherung, wenn mehr als 1 Bilder genau geprüft wurden
			if ( $force_check && static::$_valid_img_url_counter >= 1 )
			{
				return $is_valid;
			}

			// Prüfen, ob es einen gültige URL ist
			if( $this->_validation_valid_url($val) )
			{
				$is_valid = true;
			}

			// FIXME: Bilderüberprüfung kann bei vielen Bildern zu langen Ladezeiten führen. Kennt jemand eine bessere Idee?
			if( $is_valid && $force_check )
			{
				//via (@link http://stackoverflow.com/questions/676949/best-way-to-determine-if-a-url-is-an-image-in-php), thanks!

				$is_valid = false;
				$params = array('http' => array('method' => 'HEAD', 'timeout' => 5.0));
				$ctx = stream_context_create($params);

				$fp = @fopen($val, 'rb', false, $ctx);
				$meta = false;

				if ( $fp !== false )
				{
					// Timeout setzen in Sekunden
					stream_set_timeout($fp, 5);
					$meta = stream_get_meta_data($fp);
				}

				if ( $fp !== false && is_array($meta) && $meta['timed_out'] === false ) // no Problem with url or reading data from url
				{
					$wrapper_data = $meta["wrapper_data"];

					fclose($fp);

					if ( is_array($wrapper_data) )
					{
						foreach ( array_keys($wrapper_data) as $hh )
						{
							if ( substr($wrapper_data[$hh], 0, 19) == "Content-Type: image" ) // strlen("Content-Type: image") == 19
							{
								$is_valid = true;
								break;
							}
						}
					}
				}

				static::$_valid_img_url_counter++;
			}

			//Ergebnis für eine Woche cachen
			\Cache::set($cache_identifier, $is_valid, 604800);
		}

		return $is_valid;
	}

	/**
	 * Special empty method because 0 and '0' are non-empty values
	 *
	 * @param   mixed
	 * @return  bool
	 */
	protected function _empty($val)
	{
		return ($val === false or $val === null or $val === '' or $val === array());
	}

}
