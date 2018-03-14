<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * Copyright (C) 2016-2018  Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser\Visitor;

/**
 * VisitorCollection
 *
 * @since v1.4
 */

final class VisitorCollection implements VisitorCollectionInterface
{
    private $visitors = [];

    /**
     * Add a visitor
     *
     * @param Youthweb\BBCodeParser\Visitor\VisitorInterface $visitor
     */
    public function addVisitor(VisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }

    /**
     * Get all visitors
     *
     * @param Youthweb\BBCodeParser\Visitor\VisitorInterface[]
     */
    public function getVisitors()
    {
        return $this->visitors;
    }
}
