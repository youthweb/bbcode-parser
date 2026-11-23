<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Inline\AbstractInline;

/**
 * A mention of a user: the name without the `@` and the url of the profile. An extension creates it, the parser does not.
 *
 * @experimental
 */
final class Username extends AbstractInline
{
    public function __construct(
        private readonly string $name,
        private readonly string $url,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
