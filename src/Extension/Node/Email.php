<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Inline\AbstractInline;

/**
 * An email link, created by `[email]`. The address comes from the option or, without an option, from the content.
 *
 * @experimental
 */
final class Email extends AbstractInline
{
    public function __construct(private readonly string $address = '')
    {
        parent::__construct();
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
