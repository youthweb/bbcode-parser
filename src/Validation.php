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

use Cache\Adapter\Void\VoidCachePool;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Einfache Validation-Klasse, angelehnt an Fuel\Core\Validation
 */

class Validation implements ValidationInterface
{

	/**
	 * @var int Counter for image url checks
	 */
	protected static $_valid_img_url_counter = 0;

	/** CacheItemPoolInterface $cache CacheItemPool */
	protected $cache;

	/**
	 * construct
	 *
	 * @param Cache
	 * @return {11:return type}
	 */
	public function __construct(Config $config)
	{
		$cache = $config->get('cacheitempool');

		$this->cache = ($cache === null) ? new VoidCachePool() : $cache;
	}

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

		$cache_item = $this->cache->getItem($cache_identifier);

		// try to retrieve from cache and save to $is_valid var
		if ( ! $cache_item->isHit() )
		{
			$is_valid = false;

			// Keine Prüfung und Cachespeicherung, wenn mehr als 1 Bilder genau geprüft wurden
			if ( $force_check && static::$_valid_img_url_counter >= 1 )
			{
				return $is_valid;
			}

			// Prüfen, ob es einen gültige URL ist
			if ( $this->isValidUrl($val) )
			{
				$is_valid = true;
			}

			// FIXME: Bilderüberprüfung kann bei vielen Bildern zu langen Ladezeiten führen. Kennt jemand eine bessere Idee?
			if ( $is_valid and $force_check )
			{
				// via http://stackoverflow.com/questions/676949/best-way-to-determine-if-a-url-is-an-image-in-php, thanks!
				$is_valid = false;
				$params = array('http' => array('method' => 'HEAD', 'timeout' => 5.0));
				$ctx = stream_context_create($params);

				$fp = @fopen($val, 'rb', false, $ctx);
				$meta = false;

				if ( $fp !== false )
				{
					// Timeout setzen in Sekunden
					//stream_set_timeout($fp, 5);
					$meta = stream_get_meta_data($fp);
				}

				if ( $fp !== false && is_array($meta) && $meta['timed_out'] === false ) // no Problem with url or reading data from url
				{
					$wrapper_data = $meta["wrapper_data"];

					fclose($fp);

					if ( is_array($wrapper_data) or $wrapper_data instanceof \ArrayAccess )
					{
						foreach ( $wrapper_data as $hh )
						{
							if ( substr($hh, 0, 19) == "Content-Type: image" ) // strlen("Content-Type: image") == 19
							{
								$is_valid = true;
								break;
							}
						}
					}
				}

				static::$_valid_img_url_counter++;
			}

			// Ergebnis für eine Woche cachen
			$cache_item->set($is_valid);
			$cache_item->expiresAfter(604800);

			$this->cache->saveDeferred($cache_item);
		}

		return $cache_item->get();
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
