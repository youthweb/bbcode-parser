<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Engine;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Engine;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\ImageFilterExtension;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\SmileyExtension;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\UsernameExtension;
use Youthweb\BBCodeParser\Tests\Fixtures\Extension\VideoFilterExtension;
use Youthweb\BBCodeParser\Tests\Fixtures\Legacy\ImageFilter;
use Youthweb\BBCodeParser\Tests\Fixtures\Legacy\UsernameVisitor;
use Youthweb\BBCodeParser\Tests\Fixtures\Legacy\VideoFilter;
use Youthweb\BBCodeParser\Visitor\VisitorCollection;
use Youthweb\BBCodeParser\Visitor\VisitorSmiley;

/**
 * Back-to-back test of the extension points (#53): the filters and visitors of 1.x against the extensions of the league engine.
 *
 * The doubles model the four cases of youthweb (image filter, video filter, username visitor, smiley visitor). The markup of
 * quotes and of `[b]` differs from 1.x as decided in #48 and #50 and is not repeated here.
 */
#[CoversClass(Engine::class)]
final class EngineExtensionBackToBackTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function imageFilterProvider(): iterable
    {
        yield 'an image id' => ['[img]42[/img]'];
        yield 'an image id with spaces' => ['[img] 42 [/img]'];
        yield 'an image id in a link' => ['[url=https://example.org][img]42[/img][/url]'];
        yield 'an image id in a text' => ['a [img]42[/img] b'];
        yield 'an url is left to the default renderer' => ['[img]https://example.org/a.png[/img]'];
        yield 'not a number is left to the default renderer' => ['[img]abc[/img]'];
        yield 'the alias' => ['[PIC]42[/PIC]'];
    }

    #[DataProvider('imageFilterProvider')]
    public function testImageFilter(string $input): void
    {
        $config = $this->config();
        $old = (new Manager())->parse($input, $this->oldConfig(['filter' => ['image' => new ImageFilter()]]));
        $new = (new Engine($config, [new ImageFilterExtension()]))->toHtml($input);

        $this->assertSame($old, $this->normalise($new));
    }

    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function videoFilterProvider(): iterable
    {
        yield 'a youtube video' => ['[video]https://youtu.be/dQw4w9WgXcQ[/video]', []];
        yield 'the short tag' => ['[v]https://youtu.be/dQw4w9WgXcQ[/v]', []];
        yield 'the youtube tag' => ['[youtube]https://youtu.be/dQw4w9WgXcQ[/youtube]', []];
        yield 'with the size of the config' => ['[v]https://youtu.be/dQw4w9WgXcQ[/v]', ['embed_maxwidth' => 640, 'embed_maxheight' => 360]];
        yield 'embedding is off' => ['[v]https://youtu.be/dQw4w9WgXcQ[/v]', ['embed_videos' => false]];
        yield 'another url is left to the default renderer' => ['[v]https://example.org/a[/v]', []];
    }

    /**
     * @param array<string, mixed> $values
     */
    #[DataProvider('videoFilterProvider')]
    public function testVideoFilter(string $input, array $values): void
    {
        $config = $this->config($values);
        $old = (new Manager())->parse($input, $this->oldConfig(['filter' => ['video' => new VideoFilter()]] + $values));
        $new = (new Engine($config, [new VideoFilterExtension($config)]))->toHtml($input);

        $this->assertSame($old, $this->normalise($new));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function usernameProvider(): iterable
    {
        yield 'a user' => ['hi @alice!'];
        yield 'at the start' => ['@alice'];
        yield 'two users' => ['@alice and @bob_2'];
        yield 'an unknown user' => ['hi @nobody'];
        yield 'in a tag' => ['[b]@alice[/b]'];
        yield 'in noparse' => ['[noparse]@alice[/noparse]'];
        yield 'in a code block' => ['[code]@alice[/code]'];
        yield 'in an address' => ['a@alice.de'];
    }

    #[DataProvider('usernameProvider')]
    public function testUsernameVisitor(string $input): void
    {
        $collection = new VisitorCollection();
        $collection->addVisitor(new UsernameVisitor());

        $old = (new Manager($collection))->parse($input, $this->oldConfig());
        $new = (new Engine($this->config(), [new UsernameExtension()]))->toHtml($input);

        $this->assertSame($old, $this->normalise($new));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function smileyProvider(): iterable
    {
        yield 'a smiley' => ['My mistake :-)'];
        yield 'two smilies' => [':-) ;-)'];
        yield 'in a tag' => ['[b]:-)[/b]'];
        yield 'in noparse' => ['[noparse]:-)[/noparse]'];
        yield 'in a code block' => ['[code]:-)[/code]'];
    }

    #[DataProvider('smileyProvider')]
    public function testSmileyVisitor(string $input): void
    {
        // The smiley visitor of the library has other urls than the extension of the test.
        $old = (new Manager())->parse($input, $this->oldConfig(['visitor' => ['smiley' => new VisitorSmiley()]]));
        $old = strtr($old, ['https://youthweb.net/vendor/smilies/smile0001.gif' => 'https://example.org/smilies/smile.gif', 'https://youthweb.net/vendor/smilies/winking.gif' => 'https://example.org/smilies/wink.gif']);
        $new = (new Engine($this->config(), [new SmileyExtension()]))->toHtml($input);

        $this->assertSame($old, $this->normalise($new));
    }

    /**
     * Intended differences: the engine leaves links alone and has no raw text in the tree.
     *
     * @return iterable<string, array{string, string, string}>
     */
    public static function differenceProvider(): iterable
    {
        yield 'a username in the text of a link' => [
            '[url=https://example.org]hi @alice[/url]',
            '<p><a target="_blank" href="https://example.org">hi <a href="https://example.org/users/1">@alice</a></a></p>',
            '<p><a target="_blank" href="https://example.org">hi @alice</a></p>',
        ];
        yield 'a smiley in the text of a link' => [
            '[url=https://example.org]hi :-)[/url]',
            '<p><a target="_blank" href="https://example.org">hi <img src="https://example.org/smilies/smile.gif" alt=":-)" title=":-)" /></a></p>',
            '<p><a target="_blank" href="https://example.org">hi :-)</a></p>',
        ];
    }

    #[DataProvider('differenceProvider')]
    public function testDifferences(string $input, string $expectedOld, string $expectedNew): void
    {
        $collection = new VisitorCollection();
        $collection->addVisitor(new UsernameVisitor());

        $old = (new Manager($collection))->parse($input, $this->oldConfig(['visitor' => ['smiley' => new VisitorSmiley()]]));
        $old = strtr($old, ['https://youthweb.net/vendor/smilies/smile0001.gif' => 'https://example.org/smilies/smile.gif']);
        $new = (new Engine($this->config(), [new UsernameExtension(), new SmileyExtension()]))->toHtml($input);

        $this->assertSame($expectedOld, $old);
        $this->assertSame($expectedNew, $new);
    }

    private function normalise(string $html): string
    {
        // The markup of the inline tags is covered by the tests of #48.
        return strtr($html, ['<strong>' => '<b>', '</strong>' => '</b>']);
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return array<string, mixed>
     */
    private function oldConfig(array $values = []): array
    {
        // The image check would ask the network.
        return array_replace_recursive(['callbacks' => ['image' => ['force_check' => false]]], $values);
    }

    /**
     * @param array<string, mixed> $values
     */
    private function config(array $values = []): Config
    {
        return (new Config())->mergeRecursive($this->oldConfig($values));
    }
}
