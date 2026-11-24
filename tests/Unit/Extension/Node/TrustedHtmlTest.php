<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Unit\Extension\Node;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Extension\Node\TrustedHtml;

#[CoversClass(TrustedHtml::class)]
final class TrustedHtmlTest extends TestCase
{
    public function testTheLiteralIsTheHtml(): void
    {
        $node = new TrustedHtml('<b>x</b>');

        $this->assertSame('<b>x</b>', $node->getLiteral());
    }
}
