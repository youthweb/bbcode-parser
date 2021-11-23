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
