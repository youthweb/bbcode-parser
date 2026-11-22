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
use Youthweb\BBCodeParser\Definition;

/**
 * Standardsatz an BBCode Definitionen
 *
 * @internal
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
        $this->definitions[] = new Definition\Bold();
        // Depreacated [F] tag
        $definition = new Definition\Bold();
        $definition->setTagName('F');
        $this->definitions[] = $definition;

        /* [i] italics tag */
        $this->definitions[] = new Definition\Italic();
        // Depreacated [K] tag
        $definition = new Definition\Italic();
        $definition->setTagName('K');
        $this->definitions[] = $definition;

        /* [u] underline tag */
        $this->definitions[] = new Definition\Underline();

        /* [code] code tag */
        $this->definitions[] = new Definition\Code();

        /* [noparse] noparse tag */
        $this->definitions[] = new Definition\Noparse();

        /* [list] list tag */
        $this->definitions[] = new Definition\ListDefinition();
        $this->definitions[] = new Definition\ListOption();

        /* [q] quote tag */
        $this->definitions[] = new Definition\Q();
        $this->definitions[] = new Definition\QOption();
        $this->definitions[] = new Definition\Quote();
        $this->definitions[] = new Definition\QuoteOption();
        // Deprecated Z tag
        $this->definitions[] = new Definition\Z();
        $this->definitions[] = new Definition\ZOption();

        /* [email] email tag */
        $this->definitions[] = new Definition\Email($config);
        $this->definitions[] = new Definition\EmailOption($config);

        /* [url] url tag */
        $this->definitions[] = new Definition\Url($config);
        $this->definitions[] = new Definition\UrlOption($config);
        $this->definitions[] = new Definition\YwlinkOption($config);

        /* [img] image tag */
        $this->definitions[] = new Definition\Image($config);
        $this->definitions[] = new Definition\Pic($config);

        /* [v] video tag */
        $this->definitions[] = new Definition\V($config);
        $this->definitions[] = new Definition\Video($config);
        $this->definitions[] = new Definition\Youtube($config);

        /* [color] color tag */
        $this->definitions[] = new Definition\Color($config);
        $this->definitions[] = new Definition\ColorOption($config);

        /* [size] size tag */
        $this->definitions[] = new Definition\Size($config);
        $this->definitions[] = new Definition\SizeOption($config);
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
