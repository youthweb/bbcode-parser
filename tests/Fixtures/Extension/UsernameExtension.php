<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures\Extension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use Youthweb\BBCodeParser\Extension\Node\Username;
use Youthweb\BBCodeParser\Extension\TextReplacementListener;

/**
 * Models a username extension of a project: `@name` of a known user becomes a link to the profile, an unknown name stays text.
 */
final class UsernameExtension extends TextReplacementListener implements ExtensionInterface
{
    private const USERS = ['alice' => 1, 'bob_2' => 2];

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, $this);
    }

    protected function replace(string $text): array
    {
        $parts = preg_split('~(?<=^|\s)(@[a-z0-9_]{3,15})~i', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        $result = [];

        foreach ($parts as $part) {
            $name = ltrim($part, '@');

            $result[] = $part[0] === '@' && isset(self::USERS[$name])
                ? new Username($name, 'https://example.org/users/' . self::USERS[$name])
                : $part;
        }

        return $result;
    }
}
