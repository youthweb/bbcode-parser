<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures\Legacy;

use JBBCode\ElementNode;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Filter\FilterException;
use Youthweb\BBCodeParser\Filter\FilterInterface;

/**
 * Models an image filter of a project for `Manager`, the counterpart of `ImageFilterExtension`.
 */
final class ImageFilter implements FilterInterface
{
    public function setConfig(Config $config): self
    {
        return $this;
    }

    public function execute(ElementNode $el): string
    {
        $content = '';

        foreach ($el->getChildren() as $child) {
            $content .= $child->getAsText();
        }

        $id = trim($content);

        if (! ctype_digit($id)) {
            throw new FilterException();
        }

        $img = '<img class="img-responsive" src="https://files.example.org/' . $id . '.png" />';

        if ($el->closestParentOfType('url')) {
            return $img;
        }

        return '<a href="https://files.example.org/' . $id . '/full.png" target="_blank">' . $img . '</a>';
    }
}
