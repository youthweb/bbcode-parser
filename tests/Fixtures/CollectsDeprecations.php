<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures;

/**
 * Collects the messages of the user deprecations that a call triggers, also of the silenced ones.
 */
trait CollectsDeprecations
{
    /**
     * @return list<string>
     */
    private function deprecationsOf(\Closure $call): array
    {
        $messages = [];

        set_error_handler(static function (int $level, string $message) use (&$messages): bool {
            $messages[] = $message;

            return true;
        }, E_USER_DEPRECATED);

        try {
            $call();
        } finally {
            restore_error_handler();
        }

        return $messages;
    }
}
