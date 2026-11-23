<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * A list, created by `[list]` or `[list=…]`. The children are `BBListItem` nodes.
 *
 * @experimental
 */
final class BBList extends AbstractBlock
{
    public function __construct(private readonly ?string $option = null)
    {
        parent::__construct();
    }

    /**
     * The raw option of the tag, it decides about the type of the list.
     */
    public function getOption(): ?string
    {
        return $this->option;
    }
}
