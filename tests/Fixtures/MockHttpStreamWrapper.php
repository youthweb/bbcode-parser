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

namespace Youthweb\BBCodeParser\Tests\Fixtures;

/**
 * Class MockHttpStreamWrapper
 *
 * Stunt double of the PHP HTTP stream wrapper.
 *
 * @author Mykola Bespaliuk
 *
 * @see https://gist.github.com/ukolka/8448362
 */

class MockHttpStreamWrapper implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * Using static properties here because it's easy to set them up
     * before running request and in stream_open method corresponding
     * object properties are overridden with the
     * contents of the static properties here.
     */
    public static $mockBodyData = '';

    public static $mockMetaData = '';

    public static $mockResponseCode = 'HTTP/1.1 200 OK';

    public $context;

    public $position = 0;

    public $bodyData = 'test body data';

    public $responseCode = '';

    /**
     * @var array
     *            Example:
     *            array(
     *            0 => 'HTTP/1.0 301 Moved Permantenly',
     *            1 => 'Cache-Control: no-cache',
     *            2 => 'Connection: close',
     *            3 => 'Location: http://example.com/foo.jpg',
     *            4 => 'HTTP/1.1 200 OK',
     *            ...
     */
    protected $metaData = [];

    /* IteratorAggregate */
    public function getIterator()
    {
        return new \ArrayIterator($this->metaData);
    }

    /* ArrayAccess */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->metaData);
    }

    public function offsetGet($offset)
    {
        return $this->metaData[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->metaData[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->metaData[$offset]);
    }

    /* Countable */
    public function count()
    {
        return count($this->metaData);
    }

    /* StreamWrapper */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->bodyData = self::$mockBodyData;
        $this->responseCode = self::$mockResponseCode;
        $this->metaData = self::$mockMetaData;
        array_push($this->metaData, self::$mockResponseCode);

        return true;
    }

    public function stream_read($count)
    {
        if ($this->position > strlen($this->bodyData)) {
            return false;
        }

        $result =  substr($this->bodyData, $this->position, $count);
        $this->position += $count;

        return $result;
    }

    public function stream_eof()
    {
        return $this->position >= strlen($this->bodyData);
    }

    public function stream_stat()
    {
        return ['wrapper_data' => ['test']];
    }

    public function stream_tell()
    {
        return $this->position;
    }
}
