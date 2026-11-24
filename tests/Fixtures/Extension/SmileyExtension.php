<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures\Extension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use Youthweb\BBCodeParser\Extension\Node\Smiley;
use Youthweb\BBCodeParser\Extension\Node\TrustedHtml;
use Youthweb\BBCodeParser\Extension\TextReplacementListener;

/**
 * Models a smiley extension of a project: codes become smiley images, a unicode emoji becomes the HTML of an emoji library.
 */
final class SmileyExtension extends TextReplacementListener implements ExtensionInterface
{
    private const SMILIES = [
        ':-)' => 'https://example.org/smilies/smile.gif',
        ';-)' => 'https://example.org/smilies/wink.gif',
    ];

    public function __construct(private readonly int $priority = 0) {}

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, $this, $this->priority);
    }

    protected function replace(string $text): array
    {
        $parts = preg_split('~(:-\)|;-\)|😀)~u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        $result = [];

        foreach ($parts as $part) {
            $result[] = match (true) {
                isset(self::SMILIES[$part]) => new Smiley($part, self::SMILIES[$part]),
                $part === '😀' => new TrustedHtml('<img class="emoji" alt="😀" src="https://example.org/emoji/1f600.png">'),
                default => $part,
            };
        }

        return $result;
    }
}
