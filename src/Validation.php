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
     *
     * @return {11:return type}
     */
    public function __construct(Config $config)
    {
        $cache = $config->get('cacheitempool');

        $this->cache = (! is_object($cache) or ! $cache instanceof CacheItemPoolInterface) ? new VoidCachePool() : $cache;
    }

    /**
     * Validate email using PHP's filter_var()
     *
     * @param   string
     * @param mixed $val
     *
     * @return bool
     */
    public function isValidEmail($val)
    {
        return $this->_empty($val) || filter_var($val, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate URL using PHP's filter_var()
     *
     * @param   string
     * @param mixed $val
     *
     * @return bool
     */
    public function isValidUrl($val)
    {
        return $this->_empty($val) || filter_var($val, FILTER_VALIDATE_URL);
    }

    /**
     * Validierung einer URL zu einem Bild
     *
     * @param mixed $val
     * @param mixed $force_check
     */
    public function isValidImageUrl($val, $force_check = true)
    {
        $cache_identifier = 'img_urls.' . md5($val);

        $cache_item = $this->cache->getItem($cache_identifier);

        // try to retrieve from cache and save to $is_valid var
        if (! $cache_item->isHit()) {
            $is_valid = false;

            // Keine Prüfung und Cachespeicherung, wenn mehr als 1 Bilder genau geprüft wurden
            if ($force_check && static::$_valid_img_url_counter >= 1) {
                return $is_valid;
            }

            // Prüfen, ob es einen gültige URL ist
            if ($this->isValidUrl($val)) {
                $is_valid = true;
            }

            // FIXME: Bilderüberprüfung kann bei vielen Bildern zu langen Ladezeiten führen. Kennt jemand eine bessere Idee?
            if ($is_valid and $force_check) {
                // via http://stackoverflow.com/questions/676949/best-way-to-determine-if-a-url-is-an-image-in-php, thanks!
                $is_valid = false;
                $params = ['http' => ['method' => 'HEAD', 'timeout' => 5.0]];
                $ctx = stream_context_create($params);

                $fp = @fopen($val, 'rb', false, $ctx);
                $meta = false;

                if ($fp !== false) {
                    // Timeout setzen in Sekunden
                    //stream_set_timeout($fp, 5);
                    $meta = stream_get_meta_data($fp);
                }

                if ($fp !== false && is_array($meta) && $meta['timed_out'] === false) { // no Problem with url or reading data from url
                    $wrapper_data = $meta['wrapper_data'];

                    fclose($fp);

                    if (is_array($wrapper_data) or $wrapper_data instanceof \ArrayAccess) {
                        foreach ($wrapper_data as $hh) {
                            if (substr($hh, 0, 19) == 'Content-Type: image') { // strlen("Content-Type: image") == 19
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
     * @param mixed $val
     *
     * @return bool
     */
    protected function _empty($val)
    {
        return ($val === false or $val === null or $val === '' or $val === []);
    }
}
