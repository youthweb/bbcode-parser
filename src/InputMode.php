<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser;

/**
 * The syntax of a text. A text is in one mode, the modes are not mixed.
 *
 * @experimental
 */
enum InputMode
{
    case BBCode;
    case Markdown;
}
