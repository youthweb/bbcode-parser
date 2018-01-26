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

/**
 * Implements a [Z] code definition that provides the following syntax:
 *
 * [Z=Author]
 * Quotetext
 * [/Z]
 */

class ZOption extends Z
{
    public function __construct()
    {
        parent::__construct();

        $this->useOption = true;
    }
}
