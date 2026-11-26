<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Node;

use League\CommonMark\Node\Inline\AbstractInline;

/**
 * A smiley: the code in the text and the url of its image. An extension creates it, the parser does not.
 */
final class Smiley extends AbstractInline
{
    public function __construct(
        private readonly string $code,
        private readonly string $url,
    ) {
        parent::__construct();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
