<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Unit\Extension\Node;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Extension\Node\Smiley;

#[CoversClass(Smiley::class)]
final class SmileyTest extends TestCase
{
    public function testItKeepsItsValues(): void
    {
        $node = new Smiley(':-)', 'https://example.org/a.gif');

        $this->assertSame(':-)', $node->getCode());
        $this->assertSame('https://example.org/a.gif', $node->getUrl());
    }
}
