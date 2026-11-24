<?php

declare(strict_types=1);

namespace Youthweb\BBCodeParser\Tests\Fixtures\Legacy;

use JBBCode\ElementNode;
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Filter\FilterException;
use Youthweb\BBCodeParser\Filter\FilterInterface;

/**
 * Models a video filter of a project for `Manager`, the counterpart of `VideoFilterExtension`: it embeds a youtube video and
 * reads the config keys `embed_videos`, `embed_maxwidth` and `embed_maxheight`.
 */
final class VideoFilter implements FilterInterface
{
    private ?Config $config = null;

    public function setConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function execute(ElementNode $el): string
    {
        $content = '';

        foreach ($el->getChildren() as $child) {
            $content .= $child->getAsText();
        }

        if (! $this->config instanceof Config || ! $this->config->get('embed_videos')) {
            throw new FilterException();
        }

        if (preg_match('~^https://youtu\.be/([\w-]{11})$~', trim($content), $matches) !== 1) {
            throw new FilterException();
        }

        return '<iframe width="' . (int) $this->config->get('embed_maxwidth') . '" height="' . (int) $this->config->get('embed_maxheight') . '" src="//www.youtube.com/embed/' . $matches[1] . '"></iframe>';
    }
}
