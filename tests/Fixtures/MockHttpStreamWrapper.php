<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
