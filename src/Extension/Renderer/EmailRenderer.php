<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Extension\Renderer;

use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Extension\Node\Email;
use Youthweb\BBCodeParser\Html;

/**
 * Renders `[email]` like 1.x: an invalid address leaves the plain content.
 *
 * @experimental
 * @internal
 */
final readonly class EmailRenderer implements NodeRendererInterface
{
    public function __construct(private Config $config) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Email) {
            throw new \InvalidArgumentException('Incompatible node type: ' . $node::class);
        }

        $content = $childRenderer->renderNodes($node->children());
        $email = trim($node->getAddress());

        if ($email === '') {
            foreach ($node->children() as $child) {
                if ($child instanceof Text) {
                    $email .= $child->getLiteral();
                }
            }
        }

        if ($content === '' && $email === '') {
            return '';
        }

        // The address ends up in an attribute and in a javascript string: only plain address characters are allowed
        if (! $this->config->getValidation()->isValidEmail($email) || preg_match('~^[\w.!#$%&\'*+/=?^`{|}\~@-]+$~D', $email) !== 1) {
            return $content;
        }

        // Protect the address from bots?
        if ($this->config->get('callbacks.email_content.protect_email')) {
            // The text lands in a javascript string: quotes, backslashes and line breaks are written as entities
            $content = strtr($content, ['\\' => '&#92;', "'" => '&#39;', "\r" => '&#13;', "\n" => '&#10;']);

            return Html::mail_to_safe($email, $content);
        }

        return Html::mail_to($email, $content);
    }
}
