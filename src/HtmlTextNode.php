<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser;

use JBBCode\TextNode;

/**
 * A text node whose value is finished HTML, for example a link that a visitor made from a bare url.
 *
 * A definition that escapes the text of its content leaves this node as it is.
 *
 * @internal
 */
final class HtmlTextNode extends TextNode {}
