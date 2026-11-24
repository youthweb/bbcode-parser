<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Inline\AbstractStringContainer;

/**
 * A video, created by `[video]`, `[v]` and `[youtube]`. The literal is the raw content of the tag.
 *
 * @experimental
 */
final class Video extends AbstractStringContainer {}
