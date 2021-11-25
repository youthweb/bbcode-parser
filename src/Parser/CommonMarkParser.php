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

namespace Youthweb\BBCodeParser\Parser;

use Youthweb\BBCodeParser\Extension\BBCode\BBCodeExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;

/**
 * CommonMarkParser
 */
final class CommonMarkParser
{
    /**
     * Create the Parser
     */
    public static function create(): CommonMarkParser
    {
        return new self();
    }

    private MarkdownParser $markdownParser;

    private HtmlRenderer $htmlRenderer;

    /**
     * Create the Parser
     */
    private function __construct()
    {
        $config = [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 5,
            'renderer' => [
                'block_separator' => \PHP_EOL,
                'inner_separator' => \PHP_EOL,
                'soft_break'      => \PHP_EOL,
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new BBCodeExtension());

        $this->markdownParser = new MarkdownParser($environment);
        $this->htmlRenderer   = new HtmlRenderer($environment);
    }

    /**
     * parse $test with BBCode to Html
     */
    public function parseBbcodeToHtml(string $text): string
    {
        $documentAST = $this->markdownParser->parse($text);

        $content = $this->htmlRenderer->renderDocument($documentAST);

        return trim($content->getContent());
    }
}
