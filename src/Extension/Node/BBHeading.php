<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * A heading of the tags `[h1]` to `[h6]`. Its children are inline nodes.
 *
 * Unlike a Markdown heading it is rendered with the level as written, the config `heading_offset` does not apply.
 */
final class BBHeading extends AbstractBlock
{
    public function __construct(private readonly int $level)
    {
        parent::__construct();
    }

    public function getLevel(): int
    {
        return $this->level;
    }
}
