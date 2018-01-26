<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\Filter;

use JBBCode\ElementNode;
use Youthweb\BBCodeParser\Config;

/**
 * Filter interface
 */

interface FilterInterface
{
    /**
     * Set the config
     *
     * @param Config $config The config object
     *
     * @return self
     */
    public function setConfig(Config $config);

    /**
     * Execute the filter
     *
     *
     * @param ElementNode $el The element node
     *
     * @throws Filter_Exception If the node can't be filtered
     *
     * @return string HTML
     */
    public function execute(ElementNode $el);
}
