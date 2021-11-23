<?php
/*
 * A BBCode-to-HTML parser for youthweb.net
 * Copyright (C) 2016-2019  Youthweb e.V. <info@youthweb.net>

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
use Youthweb\BBCodeParser\Definition;

/**
 * Standardsatz an BBCode Definitionen
 */

class DefaultSet implements CodeDefinitionSet
{
    /** @var array Die Definitionen */
    protected $definitions = [];

    /**
     * Definiert die Definitionen
     */
    public function __construct(Config $config)
    {
        /* [b] bold tag */
        array_push($this->definitions, new Definition\Bold());
        // Depreacated [F] tag
        $builder = new CodeDefinitionBuilder('b', '<b>{param}</b>');
        $builder->setTagName('F');
        array_push($this->definitions, $builder->build());

        /* [i] italics tag */
        $builder = new CodeDefinitionBuilder('i', '<i>{param}</i>');
        array_push($this->definitions, $builder->build());
        // Depreacated [K] tag
        $builder->setTagName('K');
        array_push($this->definitions, $builder->build());

        /* [u] underline tag */
        $builder = new CodeDefinitionBuilder('u', '<u>{param}</u>');
        array_push($this->definitions, $builder->build());

        /* [code] code tag */
        array_push($this->definitions, new Definition\Code());

        /* [noparse] noparse tag */
        array_push($this->definitions, new Definition\Noparse());

        /* [list] list tag */
        array_push($this->definitions, new Definition\ListDefinition());
        array_push($this->definitions, new Definition\ListOption());

        /* [q] quote tag */
        array_push($this->definitions, new Definition\Q());
        array_push($this->definitions, new Definition\QOption());
        array_push($this->definitions, new Definition\Quote());
        array_push($this->definitions, new Definition\QuoteOption());
        // Deprecated Z tag
        array_push($this->definitions, new Definition\Z());
        array_push($this->definitions, new Definition\ZOption());

        /* [email] email tag */
        array_push($this->definitions, new Definition\Email($config));
        array_push($this->definitions, new Definition\EmailOption($config));

        /* [url] url tag */
        array_push($this->definitions, new Definition\Url($config));
        array_push($this->definitions, new Definition\UrlOption($config));
        array_push($this->definitions, new Definition\YwlinkOption($config));

        /* [img] image tag */
        array_push($this->definitions, new Definition\Image($config));
        array_push($this->definitions, new Definition\Pic($config));

        /* [v] video tag */
        array_push($this->definitions, new Definition\V($config));
        array_push($this->definitions, new Definition\Video($config));
        array_push($this->definitions, new Definition\Youtube($config));

        /* [color] color tag */
        array_push($this->definitions, new Definition\Color($config));
        array_push($this->definitions, new Definition\ColorOption($config));

        /* [size] size tag */
        array_push($this->definitions, new Definition\Size($config));
        array_push($this->definitions, new Definition\SizeOption($config));
    }

    /**
     * Returns an array of the default code definitions.
     */

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
