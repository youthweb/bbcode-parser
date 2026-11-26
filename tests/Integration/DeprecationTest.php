<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Engine;
use Youthweb\BBCodeParser\InputMode;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Tests\Fixtures\CollectsDeprecations;

/**
 * The deprecation of the parser built on `jbbcode/jbbcode` (#147).
 */
#[CoversClass(Manager::class)]
#[CoversClass(Config::class)]
final class DeprecationTest extends TestCase
{
    use CollectsDeprecations;

    private const MANAGER = 'Since youthweb/bbcode-parser 1.10.0: "Youthweb\\BBCodeParser\\Manager" is deprecated, use "Youthweb\\BBCodeParser\\Engine" instead.';

    public function testCreatingTheManagerTriggersADeprecation(): void
    {
        $this->assertSame([self::MANAGER], $this->deprecationsOf(static fn(): Manager => new Manager()));
    }

    public function testParsingWithoutDeprecatedKeysTriggersNothingElse(): void
    {
        $manager = null;
        $this->deprecationsOf(static function () use (&$manager): void {
            $manager = new Manager();
        });

        $this->assertInstanceOf(Manager::class, $manager);
        $this->assertSame([], $this->deprecationsOf(static fn(): mixed => $manager->parse('[b]a[/b]', ['parse_headlines' => true])));
    }

    public function testTheNoticeIsSilenced(): void
    {
        $reported = null;

        set_error_handler(static function () use (&$reported): bool {
            // With the `@` operator the level is not part of the current error reporting
            $reported = (error_reporting() & E_USER_DEPRECATED) !== 0;

            return true;
        }, E_USER_DEPRECATED);

        try {
            new Manager();
        } finally {
            restore_error_handler();
        }

        $this->assertFalse($reported);
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string}> config, deprecated key
     */
    public static function deprecatedKeyProvider(): iterable
    {
        yield 'parse_default' => [['parse_default' => true], 'parse_default'];
        yield 'parse_smilies' => [['parse_smilies' => false], 'parse_smilies'];
        yield 'filter.image' => [['filter' => ['image' => null]], 'filter.image'];
        yield 'filter.video' => [['filter' => ['video' => null]], 'filter.video'];
        yield 'visitor.smiley' => [['visitor' => ['smiley' => null]], 'visitor.smiley'];
        yield 'visitor.url' => [['visitor' => ['url' => null]], 'visitor.url'];
    }

    /**
     * @param array<string, mixed> $config
     */
    #[DataProvider('deprecatedKeyProvider')]
    public function testMergingADeprecatedKeyTriggersADeprecation(array $config, string $key): void
    {
        $this->assertSame([$this->keyMessage($key)], $this->deprecationsOf(static fn(): Config => (new Config())->mergeRecursive($config)));
    }

    /**
     * @param array<string, mixed> $config
     */
    #[DataProvider('deprecatedKeyProvider')]
    public function testSettingADeprecatedKeyTriggersADeprecation(array $config, string $key): void
    {
        $value = $config;

        foreach (explode('.', $key) as $part) {
            $this->assertIsArray($value);
            $value = $value[$part];
        }

        $this->assertSame([$this->keyMessage($key)], $this->deprecationsOf(static fn(): Config => (new Config())->set($key, $value)));
    }

    public function testSettingAGroupTriggersADeprecationForEveryDeprecatedKeyInIt(): void
    {
        $this->assertSame(
            [$this->keyMessage('filter.image'), $this->keyMessage('filter.video')],
            $this->deprecationsOf(static fn(): Config => (new Config())->set('filter', ['image' => null, 'video' => null])),
        );
    }

    public function testOtherKeysTriggerNothing(): void
    {
        $this->assertSame([], $this->deprecationsOf(static function (): void {
            $config = new Config();
            $config->set('parse_urls', false);
            $config->set('filter.other', null);
            $config->mergeRecursive(['embed_videos' => false, 'visitor' => [], 'callbacks' => ['image' => ['force_check' => false]]]);
        }));
    }

    public function testTheEngineTriggersNothing(): void
    {
        $this->assertSame([], $this->deprecationsOf(static function (): void {
            $engine = new Engine();
            $engine->toHtml('[b]a[/b] http://example.org');
            $engine->toHtml('**a**', InputMode::Markdown);
        }));
    }

    private function keyMessage(string $key): string
    {
        return sprintf('Since youthweb/bbcode-parser 1.10.0: the config key "%s" is deprecated, "Youthweb\\BBCodeParser\\Engine" does not read it.', $key);
    }
}
