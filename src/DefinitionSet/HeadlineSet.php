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

namespace Youthweb\BBCodeParser\DefinitionSet;

use JBBCode\CodeDefinitionSet;
use JBBCode\CodeDefinitionBuilder;
use Youthweb\BBCodeParser\Config;

/**
 * Headlinesatz an BBCode Definitionen
 */

class HeadlineSet implements CodeDefinitionSet
{
    /** @var array Die Definitionen */
    protected $definitions = [];

    /**
     * Definiert die Definitionen
     */
    public function __construct(Config $config)
    {
        /* [h1] headline 1 tag */
        $builder = new CodeDefinitionBuilder('h1', '<!-- no_p --><h1>{param}</h1><!-- no_p -->');
        array_push($this->definitions, $builder->build());

        /* [h2] headline 2 tag */
        $builder = new CodeDefinitionBuilder('h2', '<!-- no_p --><h2>{param}</h2><!-- no_p -->');
        array_push($this->definitions, $builder->build());

        /* [h3] headline 3 tag */
        $builder = new CodeDefinitionBuilder('h3', '<!-- no_p --><h3>{param}</h3><!-- no_p -->');
        array_push($this->definitions, $builder->build());

        /* [h4] headline 4 tag */
        $builder = new CodeDefinitionBuilder('h4', '<!-- no_p --><h4>{param}</h4><!-- no_p -->');
        array_push($this->definitions, $builder->build());

        /* [h5] headline 5 tag */
        $builder = new CodeDefinitionBuilder('h5', '<!-- no_p --><h5>{param}</h5><!-- no_p -->');
        array_push($this->definitions, $builder->build());

        /* [h6] headline 6 tag */
        $builder = new CodeDefinitionBuilder('h6', '<!-- no_p --><h6>{param}</h6><!-- no_p -->');
        array_push($this->definitions, $builder->build());
    }

    /**
     * Gibt die Definitionen zurück
     *
     * @return array Die Definitionen
     */
    public function getCodeDefinitions()
    {
        return $this->definitions;
    }
}
