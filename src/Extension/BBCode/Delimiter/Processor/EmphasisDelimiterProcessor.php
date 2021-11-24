<?php

declare(strict_types=1);

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

namespace Youthweb\BBCodeParser\Extension\BBCode\Delimiter\Processor;

use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Node\Inline\AbstractStringContainer;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use Youthweb\BBCodeParser\Extension\BBCode\Node\Inline\Strong;

final class EmphasisDelimiterProcessor implements DelimiterProcessorInterface
{
    /** @psalm-readonly */
    private string $char;

    /**
     * @param string $char The emphasis character to use (typically '*' or '_')
     */
    public function __construct(string $char)
    {
        $this->char = $char;
    }

    public function getOpeningCharacter(): string
    {
        return $this->char;
    }

    public function getClosingCharacter(): string
    {
        return $this->char;
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        // Calculate actual number of delimiters used from this closer
        if ($opener->getLength() >= 2 && $closer->getLength() >= 2) {
            return 2;
        }

        return 0;
    }

    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse): void
    {
        if ($delimiterUse === 2) {
            $emphasis = new Strong($this->char . $this->char);
        } else {
            return;
        }

        $next = $opener->next();
        while ($next !== null && $next !== $closer) {
            $tmp = $next->next();
            $emphasis->appendChild($next);
            $next = $tmp;
        }

        $opener->insertAfter($emphasis);
    }
}
