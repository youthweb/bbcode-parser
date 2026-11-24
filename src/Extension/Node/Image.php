<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Inline\AbstractStringContainer;

/**
 * An image, created by `[img]` and `[PIC]`. The literal is the raw content of the tag.
 *
 * @experimental
 */
final class Image extends AbstractStringContainer {}
