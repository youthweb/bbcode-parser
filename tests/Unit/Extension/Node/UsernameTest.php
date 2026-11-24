<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Unit\Extension\Node;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Extension\Node\Username;

#[CoversClass(Username::class)]
final class UsernameTest extends TestCase
{
    public function testItKeepsItsValues(): void
    {
        $node = new Username('alice', 'https://example.org/users/1');

        $this->assertSame('alice', $node->getName());
        $this->assertSame('https://example.org/users/1', $node->getUrl());
    }
}
