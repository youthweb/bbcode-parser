<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\Definition;

use Youthweb\BBCodeParser\Config;

/**
 * Implements a [YWLINK] code definition that provides the following syntax:
 *
 * [YWLINK=example.com]Click me[/YWLINK]
 */

class YwlinkOption extends UrlOption
{
    public function __construct(Config $config)
    {
        parent::__construct($config);

        $this->setTagName('YWLINK');
    }
}
