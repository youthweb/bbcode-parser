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
 * Helper Trait
 */
trait MockerTrait
{
    /**
     * Builds a ElementNode Mock
     *
     * @param mixed $content
     * @param mixed $attribute
     */
    public function buildElementNodeMock($content, $attribute)
    {
        $child = $this->getMockBuilder('JBBCode\TextNode')
            ->setConstructorArgs([$content])
            ->setMethods(null)
            ->getMock();

        $elementNode = $this->getMockBuilder('JBBCode\ElementNode')
            ->setMethods(['getAttribute', 'getChildren', 'closestParentOfType'])
            ->getMock();

        $elementNode->method('getAttribute')
            ->willReturn($attribute);

        $elementNode->method('getChildren')
            ->willReturn([$child]);

        return $elementNode;
    }
}
