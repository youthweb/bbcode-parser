<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Integration\Engine;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Engine;
use Youthweb\BBCodeParser\Manager;
use Youthweb\BBCodeParser\Tests\Fixtures\HtmlNormalizer;

/**
 * Back-to-back test: 1.x and the league engine over the synthetic corpus in `tests/Fixtures/Corpus`.
 *
 * A case without an entry in `differences.php` has the same normalised HTML in both engines, a case with an
 * entry differs, and the entry names the section of `docs/v2-html-differences.md` that explains why.
 *
 * The test enters through `Engine`; `Manager` is only the reference.
 */
#[CoversClass(Engine::class)]
final class EngineCorpusTest extends TestCase
{
    private const CORPUS = __DIR__ . '/../../Fixtures/Corpus';

    private const DIFFERENCES_DOC = __DIR__ . '/../../../docs/v2-html-differences.md';

    /**
     * @return iterable<string, array{string, string}> name, input
     */
    public static function corpusProvider(): iterable
    {
        yield from self::cases();
    }

    #[DataProvider('corpusProvider')]
    public function testEngineMatchesOldEngineOrIsListed(string $name, string $input): void
    {
        $old = HtmlNormalizer::normalize($this->renderLegacy($input));
        $new = HtmlNormalizer::normalize($this->renderEngine($input));
        $differences = $this->differences();

        if (isset($differences[$name])) {
            $this->assertNotSame($old, $new, sprintf('%s is listed as %s but is equal now, remove it from differences.php', $name, implode(', ', $differences[$name])));

            return;
        }

        $this->assertSame($old, $new, sprintf('%s differs from 1.x, fix it or list it in differences.php', $name));
    }

    public function testEveryListedCaseExists(): void
    {
        $this->assertSame([], array_values(array_diff(array_keys($this->differences()), array_keys(self::cases()))));
    }

    public function testEveryDifferenceIsExplainedInTheDocument(): void
    {
        $document = (string) file_get_contents(self::DIFFERENCES_DOC);
        preg_match_all('~^### (D\d+)\b~m', $document, $found);

        $listed = array_values(array_unique(array_merge(...array_values($this->differences()))));
        sort($listed);

        $this->assertSame($listed, $found[1], 'the sections of the document and the IDs of differences.php differ');
    }

    /**
     * @return array<string, array{string, string}>
     */
    private static function cases(): array
    {
        $cases = [];
        $root = (string) realpath(self::CORPUS);

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! $file instanceof \SplFileInfo || $file->getExtension() !== 'bbcode') {
                continue;
            }

            $name = substr($file->getPathname(), strlen($root) + 1, -strlen('.bbcode'));
            $cases[$name] = [$name, (string) file_get_contents($file->getPathname())];
        }

        ksort($cases);

        return $cases;
    }

    /**
     * @return array<string, list<string>>
     */
    private function differences(): array
    {
        /** @var array<string, list<string>> $differences */
        $differences = require self::CORPUS . '/differences.php';

        return $differences;
    }

    private function renderLegacy(string $input): string
    {
        // The image check would ask the network.
        return (new Manager())->parse($input, ['callbacks' => ['image' => ['force_check' => false]]]);
    }

    private function renderEngine(string $input): string
    {
        // The image check would ask the network.
        $config = new Config();
        $config->mergeRecursive(['callbacks' => ['image' => ['force_check' => false]]]);

        return (new Engine($config))->toHtml($input);
    }
}
