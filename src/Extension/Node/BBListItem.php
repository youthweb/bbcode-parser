<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * An item of a list, created by `[*]`. It is a container of blocks.
 *
 * @experimental
 * @internal
 */
final class BBListItem extends AbstractBlock {}
