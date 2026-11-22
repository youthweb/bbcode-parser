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
     * Builds an ElementNode stub
     *
     * @param mixed $content
     * @param mixed $attribute
     */
    public function buildElementNodeMock($content, $attribute)
    {
        $child = new \JBBCode\TextNode($content);

        $elementNode = $this->createStub(\JBBCode\ElementNode::class);

        $elementNode->method('getAttribute')
            ->willReturn($attribute);

        $elementNode->method('getChildren')
            ->willReturn([$child]);

        return $elementNode;
    }

    /**
     * Builds a Config stub that answers get() from a value map and returns
     * the real Validation from getValidation()
     *
     * createStub() stubs every method, so getValidation() has to be configured
     * for the definitions that validate through it.
     *
     * @param array<int, array<int, mixed>> $valueMap Rows of [key, default, value]
     */
    public function buildConfigStub(array $valueMap)
    {
        $config = $this->createStub(\Youthweb\BBCodeParser\Config::class);

        $config->method('get')
            ->willReturnMap($valueMap);

        $config->method('getValidation')
            ->willReturn(new \Youthweb\BBCodeParser\Validation($config));

        return $config;
    }
}
