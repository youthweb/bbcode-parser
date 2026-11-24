<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Inline\AbstractStringContainer;

/**
 * HTML that is rendered as it is, without escaping. The literal is the HTML.
 *
 * The parser never creates it, only an extension does. Use it only for HTML that the extension has
 * made itself or trusts, never for text from a user: it can carry a script.
 *
 * @experimental
 */
final class TrustedHtml extends AbstractStringContainer {}
