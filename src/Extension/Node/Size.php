<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Inline\AbstractInline;

/**
 * Text with a font size, created by `[size]` or `[size=…]`.
 *
 * @experimental
 */
final class Size extends AbstractInline
{
    public function __construct(private readonly ?string $option = null)
    {
        parent::__construct();
    }

    /**
     * The raw option of the tag, the renderer validates it.
     */
    public function getOption(): ?string
    {
        return $this->option;
    }
}
